<?php

namespace Lampion\Http;

class Client
{
    /**
     * @return string
     */
    public static function ip() {
        if (!empty($_SERVER["HTTP_CLIENT_IP"]))
            $ip = $_SERVER["HTTP_CLIENT_IP"];
        elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"]))
            $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        else
            $ip = $_SERVER["REMOTE_ADDR"];

        return $ip;
    }

    /**
     * @return mixed|string
     */
    public static function os() {
        $userAgent = $_SERVER['HTTP_USER_AGENT'];

        $osPlatform = "Unknown OS";

        $osArray = array(
            '/windows nt 10/i'      =>  'Windows 10',
            '/windows nt 6.3/i'     =>  'Windows 8.1',
            '/windows nt 6.2/i'     =>  'Windows 8',
            '/windows nt 6.1/i'     =>  'Windows 7',
            '/windows nt 6.0/i'     =>  'Windows Vista',
            '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
            '/windows nt 5.1/i'     =>  'Windows XP',
            '/windows xp/i'         =>  'Windows XP',
            '/windows nt 5.0/i'     =>  'Windows 2000',
            '/windows me/i'         =>  'Windows ME',
            '/win98/i'              =>  'Windows 98',
            '/win95/i'              =>  'Windows 95',
            '/win16/i'              =>  'Windows 3.11',
            '/macintosh|mac os x/i' =>  'Mac OS X',
            '/mac_powerpc/i'        =>  'Mac OS 9',
            '/linux/i'              =>  'Linux',
            '/ubuntu/i'             =>  'Ubuntu',
            '/iphone/i'             =>  'iPhone',
            '/ipod/i'               =>  'iPod',
            '/ipad/i'               =>  'iPad',
            '/android/i'            =>  'Android',
            '/blackberry/i'         =>  'BlackBerry',
            '/webos/i'              =>  'Mobile'
        );

        foreach ($osArray as $regex => $value)
            if (preg_match($regex, $userAgent))
                $osPlatform = $value;

        return $osPlatform;
    }

    /**
     * @return mixed|string
     */
    public static function browser() {
        $userAgent = $_SERVER['HTTP_USER_AGENT'];

        $browser = "Unknown browser";

        $browserArray = array(
            '/Trident\/7.0; rv:11.0/i' => 'Internet Explorer',
            '/msie/i'                  => 'Internet Explorer',
            '/firefox/i'               => 'Firefox',
            '/safari/i'                => 'Safari',
            '/chrome/i'                => 'Chrome',
            '/edge/i'                  => 'Edge',
            '/opera/i'                 => 'Opera',
            '/netscape/i'              => 'Netscape',
            '/maxthon/i'               => 'Maxthon',
            '/konqueror/i'             => 'Konqueror',
            '/mobile/i'                => 'Handheld Browser'
        );

        foreach ($browserArray as $regex => $value)
            if (preg_match($regex, $userAgent))
                $browser = $value;

        return $browser;
    }
}