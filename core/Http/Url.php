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
        $webRoot = WEB_ROOT;

        if(WEB_ROOT == "localhost/" || WEB_ROOT == "127.0.0.1/") {
            $webRoot = str_replace($_SERVER['DOCUMENT_ROOT'],'', getcwd());
        }

        $link = rtrim(preg_replace('#/+#', "/", $webRoot . Application::name() . "/$route" . self::processParams($params)));

        return $link;
    }

    /**
     * @param $route
     * @param array $params
     */
    public static function redirect($route, array $params = []) {
        header("Location:" . self::link($route, $params));
    }
}