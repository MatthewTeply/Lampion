<?php

namespace Lampion\Core;

class Autoloader
{
    /**
     * Registers all kernel functions, as well as current application's methods
     */
    public static function register() {
        spl_autoload_register(function($className) {
            $classNameExplode = explode("\\", $className);
            $ext = "php";

            $app = array_shift($classNameExplode);

            if($classNameExplode[0] == "Plugin") {
                $app = array_shift($classNameExplode);
            }

            $classNameImplode = implode("/", $classNameExplode);

            # If kernel classes are called
            if($app == "Lampion") {
                $source = KERNEL;
            }

            elseif($app == "Plugin") {
                $source = PLUGINS;
            }

            # If application classes are called
            else {
                $source = APP . strtolower($app) . "/src/";
            }

            if(file_exists($source . "$classNameImplode.php")) {
                include $source . "$classNameImplode.$ext";
            }
        });
    }

    public static function registerPluginFunctions() {
        $fs = new FileSystem(PLUGINS);

        $plugins = $fs->ls("", ["-dirs"]);

        foreach ($plugins as $plugin) {
            $moduleFile = $plugin['fullPath'] . "/" . strtolower($plugin['name']) . ".module";

            if(is_file($moduleFile)) {
                include_once $moduleFile;
            }
        }
    }

    public static function registerShutdownFunctions() {
        include_once KERNEL . "Misc/shutdownFunctions.php";
    }
}
