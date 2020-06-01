<?php

namespace Lampion\Core;

/**
 * Class used for registering functions at a specific time, right now only works for registering shutdown functions
 * later will be used for managing CRON tasks aswell
 * @todo CRON
 * @author Matyáš Teplý 
 */
class Scheduler
{
    /**
     * This function is for registering functions to be run at the very end of execution
     * @param string $funcName
     * @param null $class
     */
    public static function registerShutdownFunction(string $funcName, $class = null) {
        if($class === null) {
            register_shutdown_function($funcName);
        }

        else {
            register_shutdown_function([$class, $funcName]);
        }
    }
}
