<?php

namespace Lampion\Core;

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
