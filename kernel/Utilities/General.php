<?php

namespace Lampion\Utilities;

/**
 * Class containing useful code snippets, so that they can be reused
 * @author Matyáš Teplý
 */
class General {

    public static function validateJson(string $jsonString) {
        json_decode($jsonString);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    public static function replaceDoubleSlash(string $string) {
        return str_replace('//', '/', $string);
    }

    public static function strReplaceFirst($from, $to, $content) {
        $from = '/'.preg_quote($from, '/').'/';

        return preg_replace($from, $to, $content, 1);
    }

}