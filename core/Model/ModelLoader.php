<?php

namespace Lampion\Model;

use Lampion\Core\Session;

class ModelLoader
{
    /**
     * @param string $path
     * @return ucfirst
     */
    public function object(string $path, int $id = null) {
        $className = explode("/", $path);
        $className = end($className);
        $path = APP . Session::get("Lampion")['app'] . "/" . OBJECTS . "$path.obj.php";

        if(is_file($path)) {
            include_once $path;

            return new $className;
        }

        else {
            echo "Object '$className' doest not exist!";
        }
    }
}