<?php

namespace Lampion\Controller;

use Lampion\Core\Session;
use Lampion\View\View;

class ControllerLoader
{
    public $app;

    public function __construct() {
        $this->app = Session::get("lampionApp");
    }

    /**
     * @param string $longPath
     * @param string $shortPath
     * @param string $type
     * @return mixed
     * @throws \Exception
     */
    private static function loadFile(string $longPath, string $shortPath, string $type) {
        if(is_file($longPath)) {
            require_once $longPath;

            $className = explode("/", $shortPath);
            $className = end($className);
            $className = ucfirst(Session::get("lampionApp")) . "\\" . ucfirst($type) . "\\" . \ucfirst($className) . \ucfirst($type);

            return new $className;
        }

        else {
            throw new \Exception(ucfirst($type) . " '$shortPath' does not exist!");
        }
    }

    /**
     * @param string $path
     * @return mixed
     */
    public function model(string $path) {
        try {
            return self::loadFile(
                MODEL . "$path.php",
                $path,
                "model"
            );
        }

        catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param string $path
     * @return mixed
     */
    public function controller(string $path) {
        try {
            return self::loadFile(
                CONTROLLER . $path . "Controller.php",
                $path,
                "controller"
            );
        }

        catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param string $path
     * @return mixed
     */
    public function language(string $path) {
        try {
            return self::loadFile(
                LANGUAGE . "$path.lang.php",
                $path,
                "language"
            );
        }

        catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @return View
     */
    public function view() {
        return new View(TEMPLATE);
    }

    /**
     * @param string $path
     * @return ucfirst
     */
    public function object(string $path, int $id = null) {
        $className = explode("/", $path);
        $className = end($className);
        $path = OBJECT . "$path.obj.php";

        if(is_file($path)) {
            include_once $path;

            return new $className($id);
        }

        else {
            echo "Object '$className' doest not exist!";
        }
    }
}