<?php

namespace Lampion\Debug;

interface DebugInterface {

    /**
     * @param string $message
     * @return mixed
     */
    public static function set(string $message);

    /**
     * @param string $message
     * @return mixed
     */
    public static function emit(string $message);
}