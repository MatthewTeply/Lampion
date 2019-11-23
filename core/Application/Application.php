<?php

namespace Lampion\Application;

use Lampion\Core\Router;
use Lampion\Core\Session;

class Application
{
    public $router;

    public function __construct() {
        $this->router = new Router();
    }
}