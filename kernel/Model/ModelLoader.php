<?php

namespace Lampion\Model;

use Lampion\Session\Main as Session;

/**
 * (LEGACY CODE, PLANNED FOR DEPRECATION)
 * @author Matyáš Teplý
 */
class ModelLoader
{
    /**
     * @param string $path
     * @return ucfirst
     */
    public function object(string $path, int $id = null) {
        $className = explode("/", $path);
        $className = end($className);
        $path = APP . Session::get("Lampion")['app'] . "/" . ENTITY . "$path.obj.php";

        if(is_file($path)) {
            include_once $path;

            return new $className;
        }

        else {
            echo "Entity '$className' doest not exist!";
        }
    }
}