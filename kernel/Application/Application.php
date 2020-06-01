<?php

namespace Lampion\Application;

use Lampion\Core\Router;
use Lampion\Session\Main as Session;

/**
 * Class containing all info about the current application
 * @todo More application information
 * @author Matyáš Teplý
 */
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
