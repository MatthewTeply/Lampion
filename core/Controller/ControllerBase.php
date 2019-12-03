<?php

namespace Lampion\Controller;

abstract class ControllerBase {

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
        return new ControllerLoader($app);
    }
}