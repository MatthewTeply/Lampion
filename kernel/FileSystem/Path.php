<?php

namespace Lampion\FileSystem;

use Lampion\Application\Application;

class Path {

    public static function getApp($path) {
        if(strpos($path, ':') !== false) {
            return explode(':', $path)[0];
        }

        else {
            return '';
        }
    }

    public static function get(string $pathString) {
        if(strpos($pathString, ':') !== false) {
            $app = APP . explode(':', $pathString)[0];

            if($app == 'system') {
                $app = '';
            }

            $path = explode(':', $pathString)[1];
        }

        else {
            $app  = APP . Application::name();
            $path = $pathString;
        }


        return ROOT . $app . '/' . $path;
    }

}