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

            $app = array_shift($classNameExplode);
            $className = implode("\\", $classNameExplode);

            if($app == "Lampion") {
                $sources = [
                    CORE
                ];
            }

            else {
                $sources = [
                    SRC
                ];
            }

            foreach ($sources as $source) {
                if(file_exists($source . "$className.php"))
                    include $source . "$className.php";
            }
        });
    }
}