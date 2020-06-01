<?php

namespace Lampion\Application;

use Dotenv\Dotenv;
use Lampion\Session\Main as Session;

/**
 * Class that initiates and gives info about all applications
 * @todo Refactoring
 * @author Matyáš Teplý
 */
class ApplicationManager
{
    public static function init() {
        $app = Session::get("Lampion")['app'];

        require_once  APP . "$app/config/config.defaults.php";

        if(isset($_GET['url'])) {
            $_GET['url'] = rtrim($_GET['url'], '/'); // Remove trailing slashes from URL

            # If the first URL param is the app's name, remove it
            if(explode("/", $_GET['url'])[0] == $app && sizeof(explode("/", $_GET['url'])) > 1) {
                $_GET['url'] = explode("$app/", $_GET['url'])[1];
            }

            # Set default homepage
            if(!isset($_GET['url']) || $_GET['url'] == $app) {
                $_GET['url'] = DEFAULT_HOMEPAGE;
            }
        }

        else {
            $_GET['url'] = DEFAULT_HOMEPAGE;
        }

        # Require app.php, where app gets configured and all the routes get registered
        require_once APP . "$app/app.php";
    }

    public static function apps() {
        $appDirs = scandir(APP);
        $apps = [];

        foreach($appDirs as $key => $dir) {
            if($dir[0] !== ".") {
                if(is_file(APP . "$dir/app.manifest.json")) {
                    $apps[$dir] = json_decode(file_get_contents(APP . "$dir/app.manifest.json"));
                }
            }
        }

        return $apps;
    }
}
