<?php

namespace Lampion\Controller;

abstract class ControllerBase {

    /**
     * @return mixed
     */
    abstract public function index();

    /**
     * Returns an instance of ControllerLoader, so ControllerBase can load Model, other Controller, etc.
     * @return ControllerLoader
     */
    public function load() {
        return new ControllerLoader();
    }
}