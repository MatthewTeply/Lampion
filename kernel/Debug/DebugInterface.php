<?php

namespace Lampion\Debug;

/**
 * Interface, defining what a debug class should look like
 * @author Matyáš Teplý
 */
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