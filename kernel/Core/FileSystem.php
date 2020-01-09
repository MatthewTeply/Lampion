<?php

namespace Lampion\Core;

use Lampion\Application\Application;

class FileSystem {

    private $storagePath;

    public function __construct() {
        $this->storagePath = ROOT . APP . Application::name() . "/" . STORAGE;
    }

    /**
     * @param $file
     * @param $dir
     * @param array $allowedExts
     * @return bool
     * @throws \Exception
     */
    public function upload($file, $dir, $allowedExts = array()) {
        $fileName       = $file['name'];
        $fileSize       = $file['size'];
        $fileTmpName    = $file['tmp_name'];
        $fileType       = $file['type'];
        $fileError      = $file['error'];

        $fileExtenstion = explode('.', $fileName);
        $fileExtenstion = strtolower(end($fileExtenstion));

        if(!empty($allowedExts) && !in_array($fileExtenstion, $allowedExts)) {
            throw new \Exception("File extension .$fileExtenstion is not allowed!");
        }

        $uploadPath = $this->storagePath . $dir . basename($fileName);

        if(is_file($uploadPath)) {
            throw new \Exception("File '$uploadPath' already exists!");
        }

        if($fileError) {
            throw new \Exception($fileError);
        }

        if($fileSize > MAX_FILESIZE) {
            throw new \Exception("File size exceeded file size limit!");
        }

        if(move_uploaded_file($fileTmpName, $uploadPath)) {
            return true;
        }

        else {
            throw new \Exception("There was an error moving the file!");
        }
    }

    /**
     * Returns a path of a file, if it is found in app's storage
     * @param string $file
     * @return string
     * @throws \Exception
     */
    public function path(string $file): string {
        $path = $this->storagePath . $file;

        if(!is_file($path)) {
            throw new \Exception("'$file' does not exist!");
        }

        return $path;
    }

    /**
     * @param string $file
     * @param string $path
     * @return bool
     * @throws \Exception
     */
    public function mv(string $file, string $path): bool {
        if(!rename($this->storagePath . $file, $this->storagePath . $path)) {
            throw new \Exception("File could not be moved!");
        }

        return true;
    }

    /**
     * @param string $file
     * @return bool
     * @throws \Exception
     */
    public function rm(string $file): bool {
        if(!unlink($this->storagePath . $file)) {
            throw new \Exception("File could not be deleted!");
        }

        return true;
    }

    /**
     * @param string $dir_name
     * @param bool $listDirs
     * @return array
     */
    public function listFiles(string $dir_name, bool $listDirs = false): array {
        if(!is_dir($dir_name)) {
            return [];
        }

        $dir = scandir($dir_name);

        $real_path = explode("\\", realpath($dir_name));

        if(!$listDirs || end($real_path) == STORAGE) {
            $dir = array_diff($dir, array(".", ".."));
        }

        else {
            $dir = array_diff($dir, array("."));
        }

        $dir = array_values($dir);
        $dir_final = array();

        foreach($dir as $file) {
            if(is_dir($dir_name . $file) && !$listDirs) {
                continue;
            }

            $file_ext = "";

            if(strpos($file, ".")) {
                $file_ext = explode(".", $file);
                $file_ext = end($file_ext);
                $isDir = false;
            }

            else {
                $isDir = true;
            }

            $dir_final[] = array(
                "name"  => $file,
                "ext"   => $file_ext,
                "isDir" => $isDir
            );
        }

        return $dir_final;
    }

    /**
     * @param string $dir_name
     * @return array
     * @throws \Exception
     */
    public function listDirs(string $dir_name) {
        if(!is_dir($dir_name)) {
            throw new \Exception("'$dir_name' is not a directory!");
        }

        $dir = scandir($dir_name);

        $dir = array_diff($dir, array(".", ".."));

        $dir = array_values($dir);
        $dir_final = array();

        foreach($dir as $file) {
            if(!is_dir($dir_name . $file)) {
                continue;
            }

            $dir_final[] = array(
                "name"  => $file
            );
        }

        return $dir_final;
    }

    /**
     * @param string $file
     * @param string $name
     * @return bool
     * @throws \Exception
     */
    public function rename(string $file, string $name): bool {
        if(!rename($this->storagePath . $file, $this->storagePath . $name)) {
            throw new \Exception("File '$file' could not be renamed!");
        }

        return true;
    }

    /**
     * Returns size of a directory
     * @param string $dir
     * @return float
     */
    public function dirSize(string $dir): float {
        $files = $this->listFiles($this->storagePath . $dir);
        $total_size = 0;

        foreach($files as $file) {
            $total_size += filesize($this->storagePath . $dir . "/" . $file['name']);
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
     * @param string $dirName
     * @return bool
     * @throws \Exception
     */
    public function mkdir(string $dirName): bool {
        if(!mkdir($this->storagePath . htmlspecialchars($dirName), 0777, true)) {
            throw new \Exception("Directory '$dirName' could not be created!");
        }

        return true;
    }

    /**
     * @param $dir
     * @return bool
     */
    function rmdir($dir) {
        $dir = $this->storagePath . $dir;

        $i = new DirectoryIterator($dir);

        foreach($i as $f) {
            if($f->isFile()) {
                unlink($f->getRealPath());
            } else if(!$f->isDot() && $f->isDir()) {
                self::deleteDir($f->getRealPath());
            }
        }

        rmdir($dir);
        return true;
    }

}