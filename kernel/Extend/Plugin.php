<?php

namespace Lampion\Extend;

use Lampion\Controller\ControllerLoader;
use Lampion\View\View;

class Plugin
{
    private static function getCaller(array $backtrace) {
        return explode("/", explode(PLUGINS, $backtrace[0]['file'])[1])[0];
    }

    public static function view() {
        return new View(PLUGINS . self::getCaller(debug_backtrace()) . TEMPLATES, self::getCaller(debug_backtrace()), true);
    }

    public static function script(string $path) {
        ControllerLoader::script(PLUGINS . self::getCaller(debug_backtrace()) . SCRIPTS . $path);
    }
}
