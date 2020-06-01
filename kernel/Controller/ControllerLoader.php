<?php

namespace Lampion\Controller;

use Lampion\Debug\Error;
use Lampion\View\View;
use Lampion\Session\Lampion as Session;

/**
 * (LEGACY CODE) Class used for loading files inside a controller
 * @todo Refactoring, legacy code cleanup
 * @author Matyáš Teplý
 */
class ControllerLoader
{
    public $app;

    public function __construct(string $app = null) {
        $this->app = $app ?? Session::get('app');
    }

    /**
     * @param string $longPath
     * @param string $shortPath
     * @param string $type
     * @return mixed
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
            return false;
            //TODO: Error::set("File '$longPath' does not exist!");
        }
    }

    /**
     * @param string $path
     * @return mixed
     */
    public function model(string $path) {
        return $this->loadFile(
            APP . "$this->app/src/Model/$path" . "Model.php",
            $path,
            "Model"
        );
    }

    /**
     * @param string $path
     * @return mixed
     */
    public function controller(string $path) {
        return $this->loadFile(
            APP . "$this->app/src/Controller/$path" . "Controller.php",
            $path,
            "controller"
        );
    }

    /**
     * @param string $path
     * @return mixed
     */
    public function language(string $path) {
        return $this->loadFile(
            APP . "$this->app/var/language/$path.lang.php",
            $path,
            "language"
        );
    }

    /**
     * Loads js
     * @param string $path
     */
    public static function script(string $path) {
        $path = WEB_ROOT . $path;

        if(!in_array($path, $_SESSION['Lampion']['scripts'])) {
            $_SESSION['Lampion']['scripts'][] = $path;
        }
    }

    /**
     * @return View
     */
    public function view() {
        return new View(APP . "$this->app/public/templates", $this->app);
    }
}
