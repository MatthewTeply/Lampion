<?php
/**
 * Lampion Framework File System by Matyáš Teplý - 2020
 * Basic filesystem written from scratch
 * 
 * TODO:
 * Replace vanilla PHP Exceptions with Lampion Exceptions, so they can be caught by Monitor
 * Download method
 */

namespace Lampion\FileSystem;

use Error;
use Exception;
use Lampion\Application\Application;
use Lampion\Database\Query;
use Lampion\Entity\EntityManager;
use Lampion\FileSystem\Entity\Dir;
use Lampion\FileSystem\Entity\File;
use Lampion\Language\Translator;
use Lampion\Misc\Util;
use Lampion\Session\Lampion as LampionSession;
use Lampion\User\Entity\User;
use stdClass;

/**
 * Class for interacting with app's filesystem, though it can be used for the entire server by specifying storage path
 * @author Matyáš Teplý
 */
class FileSystem {

    private $storagePath;

    private $em;
    private $user;

    /**
     * FileSystem constructor. If the storagePath param remains null, it defaults to current app's storage directory
     * @param null $storagePath
     */
    public function __construct($storagePath = null) {
        $this->em   = new EntityManager();
        $this->user = unserialize(LampionSession::get('user')) ?? null;

        if($storagePath === null) {
            $this->storagePath = ROOT . APP . Application::name() . STORAGE;
        }

        else {
            $this->storagePath = $storagePath;
        }
    }

    /**
     * @param $file
     * @param string $dir
     * @param array $allowedExts
     * @return bool
     * @throws Exception
     */
    public function upload($file, string $dir, array $allowedExts = []) {
        $fileName       = $file['name'];
        $fileSize       = $file['size'];
        $fileTmpName    = $file['tmp_name'];
        $fileType       = $file['type'];
        $fileError      = $file['error'];

        if(empty($fileName)) {
            return;
        }

        $fileExtenstion = explode('.', $fileName);
        $fileExtenstion = strtolower(end($fileExtenstion));

        if(!empty($allowedExts) && !in_array($fileExtenstion, $allowedExts)) {
            throw new Exception('File extension ' . $fileExtenstion . ' is not allowed!');
        }

        $uploadPath = $this->storagePath . $dir . basename($fileName);

        /*
        if(is_file($uploadPath)) {
            throw new Exception('File '$uploadPath' already exists!');
        }
        */

        if($fileError) {
            throw new Exception($fileError);
        }

        if($fileSize > MAX_FILESIZE) {
            throw new Exception('File size exceeded file size limit!');
        }

        if(move_uploaded_file($fileTmpName, $uploadPath)) {
            $fileCheck = $this->em->findBy(File::class, [
                'fullPath' => $uploadPath
            ]);

            if(!$fileCheck) {
                $fileEntity = new File();
    
                $fileEntity->filename     = basename($fileName);
                $fileEntity->fullPath     = Util::replaceDoubleSlash($uploadPath);
                $fileEntity->relativePath = ltrim($dir . basename($fileName), '/');
                $fileEntity->extension    = $fileExtenstion;
                $fileEntity->note         = '';
                $fileEntity->tags         = '[]';
                $fileEntity->metadata     = '[]';
                $fileEntity->user         = $this->user;
    
                $this->em->persist($fileEntity);
            }

            return $dir . basename($fileName);
        }

        else {
            throw new Exception("There was an error moving the file!");
        }
    }

    /**
     * Returns a path of a file, if it is found in app's storage
     * @param string $path
     * @return string
     * @throws Exception
     */
    public function path(string $path): string {
        $path = $this->storagePath . $path;

        // FIXME: Better exception
        /*
        if(!is_file($path) || !is_dir($path)) {
            throw new Exception("'$path' does not exist!");
        }
        */

        return $path;
    }

    /**
     * @param string $file
     * @param string $path
     * @return bool
     * @throws Exception
     */
    public function mv(string $file, string $path, $recursion = false): bool {
        $from = Util::replaceDoubleSlash($this->storagePath . $file);
        $to   = Util::replaceDoubleSlash($this->storagePath . ltrim($path, '/'));

        $isDir = is_dir($from);
        $class = $isDir ? Dir::class : File::class;

        if($isDir) {
            $contents = $this->ls('//' . $file . '/');

            foreach($contents['dirs'] as $content) {
                $this->mv($content['relativePath'], str_replace(ltrim($file, '/'), ltrim($path, '/'), $content['relativePath']), true);
            }

            foreach($contents['files'] as $content) {
                $this->mv($content['relativePath'], str_replace(ltrim($file, '/'), ltrim($path, '/'), $content['relativePath']), true);
            }
        }

        $entity = $this->em->findBy($class, [
            'fullPath' => $from
        ])[0];

        $entity->relativePath = ltrim($path, '/');
        $entity->fullPath     = $to;

        $this->em->persist($entity);      

        if(!$recursion) {
            rename($from, $to);
        }

        return true;
    }

    /**
     * @param string $file
     * @return bool
     * @throws Exception
     */
    public function rm(string $file, bool $rmdir = false) {
        if($rmdir) {
            if(is_dir($this->storagePath . ltrim($file, '/'))) {
                return $this->rmdir($file);
            }
        }

        $fileEntity = $this->em->findBy(File::class, [
            'fullPath' => $this->storagePath . ltrim($file, '/')
        ]);

        if($fileEntity) {
            $fileEntity = $fileEntity[0];
        }

        # Checking permission
        if($fileEntity && !$this->hasPermission($this->user, $fileEntity)) {
            throw new Exception('Current user is not the owner of this file!');
            return false;
        }

        if(strpos($file, $this->storagePath) === false) {
            $file = $this->storagePath . ltrim($file, '/');
        }

        else {
            $file = $this->storagePath . explode($this->storagePath, $file)[1];
        }

        if(!unlink($file)) {
            throw new Exception("File could not be deleted!");
        }

        else {
            if($fileEntity) {
                $this->em->destroy((object)$fileEntity);
            }

            # Searching for all uses of this file
            if(isset($fileEntity->id)) {
                $uses = Query::select('file_uses', ['*'], [
                    'file_id' => $fileEntity->id
                ]);
            }

            # Setting all references to deleted file to empty array
            if(!empty($uses[0])) {
                foreach($uses as $use) {
                    $useEntity = $this->em->find($use['entity_name'], $use['entity_id']);
    
                    $useEntity->{$use['property']} = '[]';
    
                    $this->em->persist($useEntity);
                }
            }

            # Remove file uses referring to the deleted file
            if(isset($fileEntity->id)) {
                Query::delete('file_uses', [
                    'file_id' => $fileEntity->id
                ]);
            }

        }

        return true;
    }

    /**
     * Lists directory items
     * @param string $path
     * @param array $flags
     *     -files = Lists only files
     *     -dirs  = Lists only directories
     *     <empty> = Lists both, combination of both -files and -dirs can also be used
     * @return array
     * @throws Exception
     */
    public function ls(string $path, array $flags = []) {
        $fullPath = $this->storagePath . ltrim($path, '/');

        if(is_dir($fullPath)) {
            $files = [];
            $dirs   = [];

            $items = scandir($fullPath);

            # Get rid of the . and ..
            unset($items[0]);
            unset($items[1]);

            $fileIndex = 0; 
            $dirIndex  = 0;

            foreach ($items as $item) {
                if(!in_array("--no-dirs", $flags)) {
                    if(is_file($fullPath . $item)) {
                        @$fileEntity = $this->em->findBy(File::class, [
                            'fullPath' => $fullPath . $item
                        ])[0];

                        if(!$fileEntity) {
                            $ext = explode(".", $item);
    
                            $files[$fileIndex] = [
                                'filename'     => $item,
                                'relativePath' => $path . $item,
                                'fullPath'     => $fullPath . $item,
                                'extension'    => end($ext),
                                'size'         => $this->fileSize($path . $item)
                            ];
                        }

                        else {
                            $fileUses = Query::select('file_uses', ['*'], [
                                'file_id' => $fileEntity->id
                            ], 'entity_name', 'ASC') ?? [];

                            if(!empty($fileUses[0])) {
                                $translator = new Translator(LampionSession::get('lang'));

                                foreach($fileUses as $key => $fileUse) {
                                    $entityNameEnd = explode('\\', $fileUse['entity_name']);

                                    $propertyName = $translator->read('entity/' . end($entityNameEnd))->get($fileUse['property']);

                                    $fileUse = $this->em->find($fileUse['entity_name'], $fileUse['entity_id']);

                                    $fileUses[$key]           = $fileUse;
                                    $fileUses[$key]->property = $propertyName;
                                } 
                            }

                            $files[$fileIndex]              = (array)$fileEntity;

                            if(is_object($files[$fileIndex]['user'])) {
                                $files[$fileIndex]['user']->img =  (array)$this->em->find(File::class, $fileEntity->user->img);
                            }

                            else {
                                $files[$fileIndex]['user'] = null;   
                            }
                            
                            $files[$fileIndex]['synced']    = true;
                            $files[$fileIndex]['uses']      = $fileUses;
                        }

                        $fileIndex++;
                    }
                }

                if(!in_array("--no-files", $flags)) {
                    if(is_dir($fullPath . $item)) {
                        @$dirEntity = $this->em->findBy(Dir::class, [
                            'fullPath' => $fullPath . $item
                        ])[0];

                        if(!$dirEntity) {
                            $dirs[$dirIndex] = [
                                'filename'     => $item,
                                'relativePath' => $path . $item,
                                'fullPath'     => $fullPath . $item,
                                'size'         => $this->dirSize($path . "$item/")
                            ];
                        }

                        else {
                            $dirs[$dirIndex]              = (array)$dirEntity;
                            $dirs[$dirIndex]['user']->img = (array)$this->em->find(File::class, $dirEntity->user->img);
                            $dirs[$dirIndex]['synced']    = true;
                        }

                        $dirIndex++;
                    }
                }
            }

            if((in_array("-files", $flags) && in_array("-dirs", $flags)) || empty($flags)) {
                return [
                    'files' => $files,
                    'dirs'  => $dirs
                ];
            }

            elseif(!in_array("-files", $flags)) {
                return $dirs;
            }

            elseif(!in_array("-dirs", $flags)) {
                return $files;
            }
        }

        else {
            throw new Exception("'$this->storagePath$path' is not a directory!");
        }
    }

    /**
     * @param string $file
     * @param string $name
     * @return bool
     * @throws Exception
     */
    public function rename(string $file, string $name): bool {
        if(!rename($this->storagePath . $file, $this->storagePath . $name)) {
            throw new Exception("File '$file' could not be renamed!");
        }

        return true;
    }

    /**
     * Returns size of a directory
     * @param string $dir
     * @return float
     * @throws Exception
     */
    public function dirSize(string $dir): float {
        $files = $this->ls($dir, ["-files"]);
        $total_size = 0;

        foreach($files as $file) {
            $total_size += filesize($file['fullPath']);
        }

        return round((float)$total_size / 1000000, 2);
    }

    /**
     * Returns size of a file
     * @param string $file
     * @return false|float
     */
    public function fileSize(string $file) {
        return round((float)filesize($this->storagePath . $file) / 1000000, 2);
    }

    /**
     * @param string $path
     * @return bool
     * @throws Exception
     */
    public function mkdir(string $path): bool {
        if(!mkdir($this->storagePath . htmlspecialchars($path), 0777, true)) {
            throw new Exception("Directory '$path' could not be created!");
        }
        
        $dirEntity = new Dir();

        $fileName = explode('/', $path);

        $dirEntity->filename     = end($fileName);
        $dirEntity->fullPath     = $this->storagePath . ltrim(htmlspecialchars($path), '/');
        $dirEntity->relativePath = $path;
        $dirEntity->note         = '';
        $dirEntity->tags         = '[]';
        $dirEntity->metadata     = '[]';
        $dirEntity->user         = $this->user;

        $this->em->persist($dirEntity);

        return true;
    }

    /**
     * @param string $dir
     * @return bool
     */
    public function rmdir(string $dir) {
        if(strpos($dir, $this->storagePath) === false) {
            $dir = $this->storagePath . ltrim($dir, '/');
        }

        else {
            $dir = $this->storagePath . explode($this->storagePath, $dir)[1];
        }

        $i = new \DirectoryIterator($dir);

        foreach($i as $f) {
            if($f->isFile()) {
                $this->rm($f->getRealPath());
            } 
            
            else if(!$f->isDot() && $f->isDir()) {
                $this->rmdir($f->getRealPath());
            }
        }

        $dirEntity = $this->em->findBy(Dir::class, [
            'fullPath' => $dir
        ]);

        if($dirEntity) {
            $dirEntity = $dirEntity[0];
        }

        if($dirEntity && !$this->hasPermission($this->user, $dirEntity)) {
            throw new Exception('Current user is not the owner of directory "' . $dirEntity->relativePath . '"');
            return false;
        }

        if(!rmdir($dir)) {
            throw new Exception('Directory "' . $dirEntity->relativePath . '" could not be deleted!');
        }

        else {
            if($dirEntity) {
                $this->em->destroy((object)$dirEntity);
            }
        }

        return true;
    }

    /**
     * Copy file/directory to a new destination
     * @param string $source
     * @param string $destination
     * @return bool
     * @throws Exception
     */
    public function cp(string $source, string $destination) {
        if(!is_file($this->storagePath . $source)) {
            throw new Exception("'$source' is not a file!");
        }

        if(is_file($this->storagePath . $destination . $source)) {
            throw new Exception("'$source' already exists in '$destination'!");
        }

        if(copy($this->storagePath . $source, $this->storagePath . $destination . $source)) {
            return true;
        }

        else {
            throw new Exception("Could not copy '$source'!");
        }
    }

    /**
     * Overwrite file's data
     * @param string $file - Path to file
     * @param mixed  $data - Data to write into file
     * @return bool
     */
    public function write(string $file, $data, $chmod = 0755) {
        if(!file_exists($this->storagePath . dirname($file))) {
            $this->mkdir(dirname($file));
        }

        $fh = fopen($this->storagePath . $file, 'wb');
        
        fwrite($fh, $data);
        fclose($fh);

        chmod($this->storagePath . $file, $chmod);

        return true;
    }

    public function read(string $file) {
        $contents = file_get_contents($this->storagePath . $file);

        $readClass = new stdClass;
        $readClass->contents = $contents;

        $readClass->__toString = function() { return $this->contents; };
        $readClass->object     = json_decode($contents);
    
        return $readClass;
    }

    /**
     * Check if current user has permission to desired file/directory
     * @param User   $user
     * @param object $file - Lampion file/directory object
     */
    public function hasPermission(User $user, object $file) {
        // NOTE: I know the varibale is called file, but this works for directories aswell

        $isAdmin = in_array('ROLE_ADMIN', json_decode($user->role));

        # If file returns an object, that means file is managed
        if($file) {
            # If file has an owner, check for permission
            if($file->user) {
                # Checking for permission, only user who is the owner of the file or an admin can delete a file
                if($user != $file->user && !$isAdmin) {
                    return false;
                }
    
                else {
                    return true;
                }
            }
        }

        # If it doesn't, that means that the file was uploaded by some other means
        # in that case, only ADMINS (configurable?) should be allowed to delete it
        else {
            if(!$isAdmin) {
                return false;
            }
        }
    }

    public function exists($file) {
        return file_exists($this->storagePath . $file);
    }
}
