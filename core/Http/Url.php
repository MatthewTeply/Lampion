<?php

namespace Lampion\Http;

use Lampion\Application\Application;

class Url
{
    /**
     * @param array $params
     * @return string
     */
    public static function processParams(array $params) {
        $paramsString = "?";

        foreach ($params as $key => $value) {
            if($paramsString == "?")
                $paramsString .= "$key=$value";
            else
                $paramsString .= "&$key=$value";
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
        return rtrim(preg_replace('#/+#', "/", WEB_ROOT . Application::name() . "/$route" . self::processParams($params)));
    }

    /**
     * @param $route
     * @param array $params
     */
    public static function redirect($route, array $params = []) {
        header("Location:" . self::link($route, $params));
    }
}