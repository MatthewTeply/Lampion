<?php

namespace Lampion\Language;

use Error;
use Lampion\Core\FileSystem;
use Lampion\Application\Application;
use Lampion\Debug\Console;
use stdClass;

class Translator {

    // Public:
    public $langDir;
    public $langCode;

    // Protected:
    protected $fs;

    public function __construct(string $langCode) {
        $this->langCode = $langCode;
        $this->langDir  = ROOT . APP . Application::name() . LANG . $langCode . DIRECTORY_SEPARATOR;

        $this->fs = new FileSystem($this->langDir);
    }

    public function getSections() {
        return $this->fs->ls('')['dirs'];
    }

    public function read(string $path) {
        $readClass = new class($path, $this->fs) {
            public $json;
            public $fs;

            public function __construct(string $path, FileSystem $fs) {
                $this->fs   = $fs;
                $this->json = @json_decode(file_get_contents($this->fs->path($path . '.json')) ?? null);
            }

            public function getAll() {
                if(!$this->json) {
                    return new stdClass();
                }

                return $this->json;
            }

            public function get($key) {
                if(!$this->json) {
                    return $key;
                }

                return $this->json->$key ?? $key;
            }

            public function set($key, $value) {
                if(!$this->json) {
                    return false;
                }

                $this->json->$key = $value;
            }
        };

        return $readClass;
    }

}