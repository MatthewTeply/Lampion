<?php

namespace Lampion\Session;

/**
 * Class for managing Lampion's session
 * @author Matyáš Teplý
 */
class Lampion implements SessionInterface {

    public static function set(string $name, $value) {
        $_SESSION['Lampion'][$name] = $value;
    }

    public static function unset($name) {
        unset($_SESSION['Lampion'][$name]);
    }

    public static function get($name) {
        return $_SESSION['Lampion'][$name] ?? null;
    }
}