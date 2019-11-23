<?php

namespace Lampion\Application;

use Lampion\Core\Session;

class ApplicationManager
{
    public static function init() {
        if(isset($_GET['url'])) {
            $firstUrlParam = explode("/", $_GET['url'])[0];

            if(is_dir(APP . $firstUrlParam)) {
                $app = $firstUrlParam;

                if(is_array(explode($firstUrlParam . "/", $_GET['url'])) > 1)
                    $_GET['url'] = explode($firstUrlParam . "/", $_GET['url'])[1];
            }

            else {
                $app = DEFAULT_APP;
            }
        }

        else {
            $app = DEFAULT_APP;
        }

        Session::set("lampionApp", $app);

        # App configs
        require_once  APP . "$app/config/config.defaults.php";

        # Set default homepage
        if(!isset($_GET['url'])) {
            $_GET['url'] = DEFAULT_HOMEPAGE;
        }

        # Require app.php, where app gets configured and all the routes get registered
        require_once APP . "$app/app.php";
    }
}