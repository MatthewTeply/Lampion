<?php

namespace Lampion\Controller;

use Lampion\Core\Session;
use Lampion\View\View;

class ControllerLoader
{
    public $app;

    public function __construct(string $app = null) {
        if($app !== null) {
            $this->app = $app;
        }

        else {
            $this->app = Session::get("Lampion")['app'];
        }
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
            $className = ucfirst(Session::get("Lampion")['app']) . "\\" . ucfirst($type) . "\\" . \ucfirst($className) . \ucfirst($type);

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
                MODELS . "$path.php",
                $path,
                "Model"
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
                CONTROLLERS . $path . "Controller.php",
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
                "languages"
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
        return new View(TEMPLATES);
    }

    /**
     * @param string $path
     * @return ucfirst
     */
    public function object(string $path, int $id = null) {
        $className = explode("/", $path);
        $className = end($className);
        $path = OBJECTS . "$path.php";

        if(is_file($path)) {
            include_once $path;

            return new $className($id);
        }

        else {
            echo "Object '$className' doest not exist!";
        }
    }
}