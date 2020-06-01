<?php

namespace Lampion\Http;

use Lampion\Controller\ControllerLoader;

/**
 * General response class, containing all methods for responding to requests
 * @todo HTTP response codes
 * @author Matyáš Teplý
 */
class Response
{
    public $loader;

    public function __construct() {
        $this->loader = new ControllerLoader();
    }

    public function controller(string $path) {
        return $this->loader->controller($path);
    }

    public function view() {
        return $this->loader->view();
    }

    public function send($msg) {
        if(is_array($msg))
            print_r($msg);
        else
            echo $msg;
    }

    public function redirect(string $route, array $params = []) {
        return Url::redirect($route, $params);
    }

    public function json(array $arr) {
        echo json_encode($arr);
    }
}
