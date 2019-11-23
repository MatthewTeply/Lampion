<?php

namespace Lampion\Application;

use Lampion\Core\Router;
use Lampion\Core\Session;

class Application
{
    public $router;

    public function __construct()
    {
        $this->router = new Router();

        $firstUrlParam = explode("/", $_GET['url'])[0];

        if(is_dir(APP . $firstUrlParam)) {
            $name = $firstUrlParam;

            if(is_array(explode($firstUrlParam . "/", $_GET['url'])) > 1)
                $_GET['url'] = explode($firstUrlParam . "/", $_GET['url'])[1];
        }

        else {
            $name = DEFAULTS['app'];
        }

        Session::set("lampionApp", $name);
    }
}