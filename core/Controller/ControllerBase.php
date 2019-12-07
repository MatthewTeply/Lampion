<?php

namespace Lampion\Controller;

abstract class ControllerBase {

    protected $parentApp;

    public function __construct()
    {
        $this->parentApp =  explode("\\", get_called_class())[0];
    }

    /**
     * @return mixed
     */
    abstract public function index();

    /**
     * Returns an instance of ControllerLoader, so ControllerBase can load Model, other Controller, etc.
     * @param string $app
     * @return ControllerLoader
     */
    public function load(string $app = null) {
        return new ControllerLoader($app !== null ? $app : $this->parentApp);
    }
}