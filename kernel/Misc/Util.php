<?php

namespace Lampion\Misc;

class Util {

    public static function validateJson(string $jsonString) {
        json_decode($jsonString);
        return (json_last_error() == JSON_ERROR_NONE);
    }

}