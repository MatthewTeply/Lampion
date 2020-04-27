<?php

namespace Lampion\Http;

use Lampion\Controller\ControllerLoader;

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
