<?php

namespace Lampion\Controller;

use Lampion\Core\Session;
use Lampion\View\View;

class ControllerLoader
{
    public $app;

    public function __construct(string $app = null) {
        $this->app = $app;
    }

    /**
     * @param string $longPath
     * @param string $shortPath
     * @param string $type
     * @return mixed
     * @throws \Exception
     */
    private function loadFile(string $longPath, string $shortPath, string $type) {
        if(is_file($longPath)) {
            require_once $longPath;

            $className = explode("/", $shortPath);
            $className = end($className);
            $className = ucfirst($this->app) . "\\" . ucfirst($type) . "\\" . \ucfirst($className) . \ucfirst($type);

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
            return $this->loadFile(
                APP . "$this->app/src/Model/$path" . "Model.php",
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
            return $this->loadFile(
                APP . "$this->app/src/Controller/$path" . "Controller.php",
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
            return $this->loadFile(
                APP . "$this->app/data/language/$path.lang.php",
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
        return new View(APP . "$this->app/public/templates", $this->app);
    }

    /**
     * @param string $path
     * @param int|null $id
     * @return ucfirst
     */
    public function object(string $path, int $id = null) {
        $className = explode("/", $path);
        $className = end($className);
        $path = APP . "$this->app/src/Entity/$path.php";

        if(is_file($path)) {
            include_once $path;

            return new $className($id);
        }

        else {
            echo "Entity '$className' doest not exist!";
        }
    }
}