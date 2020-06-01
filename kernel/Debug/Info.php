<?php

namespace Lampion\Debug;

/**
 * Info debug class
 * @author Matyáš Teplý
 */
class Info implements DebugInterface
{
    private static $code = "Info";

    public static function set(string $message) {
        // TODO: Store info to later be used in a debug display

        self::emit($message);
    }

    public static function emit(string $message) {
        echo self::$code . ": " . $message;
    }
}