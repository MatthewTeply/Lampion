<?php

namespace Lampion\Application;

use Lampion\Core\Router;
use Lampion\Session\Main as Session;

class Application
{
    public $router;

    public function __construct() {
        $this->router = new Router();
    }

    public static function name() {
        return $_SESSION['Lampion']['app'];
    }
}
