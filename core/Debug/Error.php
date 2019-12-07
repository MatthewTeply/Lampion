<?php

namespace Lampion\Debug;

class Error implements DebugInterface
{
    private static $code = "Error";

    public static function set(string $message) {
        // TODO: Store errors to later be used in a debug display

        self::emit($message);
    }

    public static function emit(string $message) {
        echo self::$code . ": " . $message;
    }
}