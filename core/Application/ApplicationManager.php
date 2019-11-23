<?php

namespace Lampion\Application;

use Lampion\Core\Session;

class ApplicationManager
{
    public $name;

    /**
     * ApplicationManager constructor.
     * @return $this
     */
    public function __construct() {
        $firstUrlParam = explode("/", $_GET['url'])[0];

        if(is_dir(APP . $firstUrlParam)) {
            $this->name = $firstUrlParam;
        }

        else {
            $this->name = DEFAULTS['app'];
        }

        Session::set("lampApp", $this->name);

        return $this;
    }

    public function switchControl(string $name) {
        Session::set("lampApp", $name);
    }

    public function returnControl() {
        Session::set("lampApp", $this->name);
    }
}