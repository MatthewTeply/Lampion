<?php

namespace Lampion\Core;

use Lampion\Debug\Error;

class Cookie
{
    /**
     * Sets cookie, time can either be an int, which means time is set in seconds, or an array with specified time units and their values
     * @param string $name
     * @param $value
     * @param $time Either an int unix timestamp, or array eg. ['s' => 30, 'm' => 1, 'h' => 2] (02:01:30)
     */
    public static function set(string $name, $value, $time) {
        $acceptedUnits = [
            's'  => 1,         // Seconds
            'm'  => 60,        // Minutes
            'h'  => 3600,      // Hours
            'd'  => 86400,     // Days
            'w'  => 604800.02, // Weeks
            'mo' => 2629800,   // Months
            'y'  => 31557600   // Years
        ];

        $timeAmount = 0;

        if(is_array($time)) {
            foreach ($time as $unit => $timeValue) {
                if(in_array($unit, array_keys($acceptedUnits))) {
                    $timeAmount += $timeValue * $acceptedUnits[$unit];
                }

                else {
                    Error::set("Invalid time unit for cookie expiration time!");
                }
            }
        }

        else {
            $timeAmount = $time;
        }

        setcookie($name, $value, time() + $timeAmount, "/");
    }

    /**
     * @param string $name
     * @return bool
     */
    public static function exists(string $name) {
        return isset($_COOKIE[$name]) ? true : false;
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public static function get(string $name) {
        return self::exists($name) ? $_COOKIE[$name] : null;
    }

    /**
     * @param string $name
     * @return bool
     */
    public static function destroy(string $name) {
        if(self::exists($name)) {
            unset($_COOKIE[$name]);
            setcookie($name, null, -1, "/");
            return true;
        }

        else {
            Error::set("Cookie '$name' does not exist!");
        }
    }
}