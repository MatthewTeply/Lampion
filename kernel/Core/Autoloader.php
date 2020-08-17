<?php

namespace Lampion\Core;

/**
 * Class that automatically loads classes, registers shutdown and plugin functions
 * @author Matyáš Teplý
 */
class Autoloader
{
    /**
     * Registers all kernel functions, as well as current application's methods
     */
    public static function register() {
        spl_autoload_register(function($className) {
            $classNameExplode = explode('\\', $className);
            $ext = 'php';

            $app = array_shift($classNameExplode);

            $classNameImplode = implode('/', $classNameExplode);

            # If kernel classes are called
            if($app == 'Lampion') {
                $source = KERNEL;
            }

            # If application classes are called
            else {
                $source = APP . strtolower($app) . '/src/';
            }

            if(file_exists($source . $classNameImplode . '.php')) {
                include $source . $classNameImplode . '.' . $ext;
            }
        });
    }

    public static function registerShutdownFunctions() {
        include_once KERNEL . 'Misc/shutdownFunctions.php';
    }
}
