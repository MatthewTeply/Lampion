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

            $className = ucfirst($type) . implode("", explode("/", $shortPath));

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
                APP . $this->app . "/" . MODEL . "$path.model.php",
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
                APP . $this->app . "/" . CONTROLLER . "$path.controller.php",
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
                APP . $this->app . "/" . LANGUAGE . "$path.lang.php",
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
        return new View(APP . $this->app . "/" . TEMPLATE);
    }
}