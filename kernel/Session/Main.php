<?php

namespace Lampion\Session;

/**
 * Class for managing session
 * @author Matyáš Teplý
 */
class Main implements SessionInterface
{
    public static function set(string $name, $value) {
        $_SESSION[$name] = $value;
    }

    public static function unset($name) {
        unset($_SESSION[$name]);
    }

    public static function get($name) {
        return $_SESSION[$name];
    }

    public static function destroy() {
        session_destroy();
    }

    public static function id() {
        return session_id();
    }
}