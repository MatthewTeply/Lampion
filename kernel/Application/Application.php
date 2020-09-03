<?php

namespace Lampion\Application;

/**
 * Class for configuring the current application
 * @todo More application information
 * @author Matyáš Teplý
 */
class Application
{
    private $router;

    public function router(&$router) {
        $this->router = $router;
    }

    public function listen() {
        $this->router->listen();
    }

    public static function name() {
        return $_SESSION['Lampion']['app'];
    }
}
