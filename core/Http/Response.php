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
}