<?php

namespace Lampion\Http;

use Lampion\Application\Application;

/**
 * Class for easy URL generation/redirection
 * @author Matyáš Teplý
 */
class Url
{
    /**
     * @param array $params
     * @return string
     */
    public static function processParams(array $params) {
        $paramsString = "?";

        foreach ($params as $key => $value) {
            $paramTranslated = !empty($value) ? "$key=$value" : $key;

            if($paramsString == "?") {
                $paramsString .= "$paramTranslated";
            }

            else {
                $paramsString .= "&$paramTranslated";
            }
        }

        return $paramsString != "?" ? $paramsString : "";
    }

    /**
     * @param array $params
     */
    public static function reload(array $params = []) {
        $params = self::processParams($params);

        header("Location:" . $_GET['url'] . $params);
    }

    /**
     * Returns a link to a route, that can be used in links
     * @param string $route
     * @param array $params
     * @return string
     */
    public static function link(string $route, array $params = []) {
        if($route[0] != '/') {
            $appName = Application::name() != DEFAULT_APP ? Application::name() . '/' : '';
        }

        else {
            $appName = '';
            $route   = ltrim('/', $route);
        }

        $link = rtrim(WEB_ROOT . $appName . $route . self::processParams($params));

        return $link;
    }

    /**
     * @param $route
     * @param array $params
     */
    public static function redirect($route, array $params = []) {
        header("Location: " . self::link($route, $params));
    }

    public static function redirectOnce($route, array $params = []) {
        if($route != $_GET['url']) {
            header("Location: " . self::link($route, $params));
    
        }
    }

    public static function get(string $url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}