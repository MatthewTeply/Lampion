<?php

namespace Lampion\Debug;

/**
 * Error debug class
 * @author Matyáš Teplý
 */
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

    public static function errorHandler($errno, $errstr, $errfile, $errline) {
        global $_ERRORS;
        $_ERRORS[] = array("errno" => $errno, "errstr" => $errstr, "errfile" => $errfile, "errline" => $errline);
    }

    public static function shutdownHandler() {
        global $_ERRORS;

        $_ERRORS[] = error_get_last();
    }
}