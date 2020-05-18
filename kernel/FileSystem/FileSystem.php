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

use Exception;
use Lampion\Application\Application;
use Lampion\Database\Query;
use Lampion\Entity\EntityManager;
use Lampion\FileSystem\Entity\Dir;
use Lampion\FileSystem\Entity\File;
use Lampion\Session\Lampion as LampionSession;

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
            throw new Exception("File extension '.$fileExtenstion' is not allowed!");
        }

        $uploadPath = $this->storagePath . $dir . basename($fileName);

        /*
        if(is_file($uploadPath)) {
            throw new Exception("File '$uploadPath' already exists!");
        }
        */

        if($fileError) {
            throw new Exception($fileError);
        }

        if($fileSize > MAX_FILESIZE) {
            throw new Exception("File size exceeded file size limit!");
        }

        if(move_uploaded_file($fileTmpName, $uploadPath)) {
            $fileCheck = $this->em->findBy(File::class, [
                'fullPath' => $uploadPath
            ]);

            if(!$fileCheck) {
                $fileEntity = new File();
    
                $fileEntity->filename     = basename($fileName);
                $fileEntity->fullPath     = $uploadPath;
                $fileEntity->relativePath = $dir . basename($fileName);
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
    public function mv(string $file, string $path): bool {
        if(!rename($this->storagePath . $file, $this->storagePath . $path)) {
            throw new Exception("File could not be moved!");
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
            if(is_dir($this->storagePath . $file)) {
                return $this->rmdir($file);
            }
        }

        $fileEntity = $this->em->findBy(File::class, [
            'fullPath' => $this->storagePath . $file
        ]);

        $isAdmin = in_array('ROLE_ADMIN', json_decode($this->user->role));

        # If fileEntity returns an object, that means file is managed
        if($fileEntity) {
            # If file has an owner, check for permission
            if($fileEntity->user) {
                # Checking for permission, only user who is the owner of the file or an admin can delete a file
                if($this->user != $fileEntity->user && !$isAdmin) {
                    throw new Exception('Current user is not the owner of this file!');
                }
    
                else {
                    $this->em->destroy($fileEntity);
                }
            }
        }

        # If it doesn't, that means that the file was uploaded by some other means
        # in that case, only ADMINS (configurable?) should be allowed to delete it
        else {
            if(!$isAdmin) {
                throw new Exception('Only administrators are allowed to delete externally uploaded files!');
            }
        }

        if(!unlink($this->storagePath . $file)) {
            throw new Exception("File could not be deleted!");
        }

        $file = $this->em->findBy(File::class, [
            'fullPath' => $this->storagePath . ltrim($file, '/')
        ]);

        if($file) {
            $this->em->destroy($file);
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
            $dirs = [];

            $items = scandir($fullPath);

            # Get rid of the . and ..
            unset($items[0]);
            unset($items[1]);

            $fileIndex = 0;
            $dirIndex  = 0;

            foreach ($items as $item) {
                if(!in_array("--no-dirs", $flags)) {
                    if(is_file($fullPath . $item)) {
                        $fileEntity = $this->em->findBy(File::class, [
                            'fullPath' => $fullPath . $item
                        ]);

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
                            $files[$fileIndex]           = (array)$fileEntity;
                            $files[$fileIndex]['synced'] = true;
                        }

                        $fileIndex++;
                    }
                }

                if(!in_array("--no-files", $flags)) {
                    if(is_dir($fullPath . $item)) {
                        $dirEntity = $this->em->findBy(Dir::class, [
                            'fullPath' => $fullPath . $item
                        ]);

                        if(!$dirEntity) {
                            $dirs[$dirIndex] = [
                                'filename'     => $item,
                                'relativePath' => $path . $item,
                                'fullPath'     => $fullPath . $item,
                                'size'         => $this->dirSize($path . "$item/")
                            ];
                        }

                        else {
                            $dirs[$dirIndex]           = (array)$dirEntity;
                            $dirs[$dirIndex]['synced'] = true;
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
    function rmdir(string $dir) {
        $dir = $this->storagePath . $dir;

        $i = new \DirectoryIterator($dir);

        foreach($i as $f) {
            if($f->isFile()) {
                $this->rm($f->getRealPath());
            } else if(!$f->isDot() && $f->isDir()) {
                self::rmdir($f->getRealPath());
            }
        }

        rmdir($dir);

        $dirEntity = $this->em->findBy(Dir::class, [
            'fullPath' => $dir
        ]);

        if($dirEntity) {
            $this->em->destroy($dirEntity);
        }

        return true;
    }

    /**
     * @param string $source
     * @param string $destination
     * @return bool
     * @throws Exception
     */
    function cp(string $source, string $destination) {
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

    function write(string $file, $data) {
        if(!file_exists($this->storagePath . dirname($file))) {
            $this->mkdir(dirname($file));
        }

        $fh = fopen($this->storagePath . $file, 'wb');
        
        fwrite($fh, $data);
        fclose($fh);

        return true;
    }
}
