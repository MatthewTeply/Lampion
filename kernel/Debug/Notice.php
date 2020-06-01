<?php

namespace Lampion\Debug;

/**
 * Notice debug class
 * @author Matyáš Teplý
 */
class Notice implements DebugInterface
{
    private static $code = "Notice";

    public static function set(string $message) {
        // TODO: Store notices to later be used in a debug display

        self::emit($message);
    }

    public static function emit(string $message) {
        echo self::$code . ": " . $message;
    }
}