<?php

namespace Lampion\Core;

class Scheduler
{
    public static function registerShutdownFunction(string $funcName, $class = null) {
        if($class === null) {
            register_shutdown_function($funcName);
        }

        else {
            register_shutdown_function([$class, $funcName]);
        }
    }
}
