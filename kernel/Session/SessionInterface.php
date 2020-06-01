<?php

namespace Lampion\Session;

/**
 * Interface, defininf what a session class should look like
 * @author Matyáš Teplý
 */
interface SessionInterface {

    /**
     * @param string $name
     * @param mixed $value
     */
    public static function set(string $namem, $value);

    /**
     * @param string $name
     */
    public static function get(string $name);
    
    /**
     * @param string $name
     */
    public static function unset(string $name);
}