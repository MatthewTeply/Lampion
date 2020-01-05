<?php

namespace Lampion\Core;

class Autoloader
{
    /**
     * Registers all core functions, as well as current application's methods
     */
    public static function register() {
        spl_autoload_register(function($className) {
            $classNameExplode = explode("\\", $className);

            $app       = array_shift($classNameExplode);
            $className = implode("/", $classNameExplode);

            # If core classes are called
            if($app == "Lampion") {
                $source = CORE;
            }

            # If application classes are called
            else {
                $source = APP . "$app/src/";
            }

            if(file_exists($source . "$className.php"))
                include $source . "$className.php";
        });
    }
}