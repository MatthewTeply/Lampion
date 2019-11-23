<?php

namespace Lampion\Core;

class Autoloader
{
    /**
     * Registers all core functions, as well as current application's methods
     */
    public static function register() {
        spl_autoload_register(function($className) {
            $className = explode("Lampion\\", $className)[1];

            if(file_exists(CORE . "$className.php"))
                include CORE . "$className.php";
        });
    }
}