<?php

namespace Lampion\Config;

use Lampion\Application\Application;
use Lampion\Debug\Console;
use Lampion\FileSystem\FileSystem;

class Registry {
    
    private static function getConfig($app) {
        $fs = new FileSystem(ROOT . APP . $app . '/' . CONFIG);

        if(!$fs->exists('registry.json')) {
            $fs->write('registry.json', '');
        }

        return $fs->read('registry.json')->object;
    }

    private static function setConfig($app, $config) {
        $fs = new FileSystem(ROOT . APP . $app . '/' . CONFIG);

        $fs->write('registry.json', json_encode($config));
    }

    private static function getApp($app) {
        # If app is not specified, use the current one
        $app = !$app ? Application::name() : $app;
        
        return $app;
    }

    public static function set($key, $value, string $app = null) {
        $app    = self::getApp($app);
        $config = self::getConfig($app);

        $config->{$key} = $value;

        self::setConfig($app, $config);
    }

    public static function get($key, string $app = null) {
        $app    = self::getApp($app);
        $config = self::getConfig($app);

        return $config->{$key};
    }

    public static function delete($key, string $app = null) {
        $app    = self::getApp($app);
        $config = self::getConfig($app);

        unset($config->{$key});

        self::setConfig($app, $config);
    }

}