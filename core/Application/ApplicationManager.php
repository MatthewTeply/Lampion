<?php

namespace Lampion\Application;

use Dotenv\Dotenv;
use Lampion\Core\Session;

class ApplicationManager
{
    public static function init() {
        $app = Session::get("Lampion")['app'];

        # App configs
        $dotenv = Dotenv::create(APP . $app);
        $dotenv->load();

        require_once  APP . "$app/config/config.php";
        require_once  APP . "$app/config/config.defaults.php";

        # Set default homepage
        if(!isset($_GET['url'])) {
            $_GET['url'] = DEFAULT_HOMEPAGE;
        }

        # Require app.php, where app gets configured and all the routes get registered
        require_once APP . "$app/app.php";
    }
}