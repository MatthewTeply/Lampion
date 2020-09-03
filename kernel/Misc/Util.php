<?php

namespace Lampion\Misc;

/**
 * Class containing useful code snippets, so that they can be reused
 * @author Matyáš Teplý
 */
class Util {

    public static function validateJson($jsonString) {
        if(gettype($jsonString) != 'string') {
            return false;
        }

        json_decode($jsonString);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    public static function replaceDoubleSlash(string $string) {
        return str_replace('//', '/', $string);
    }

}