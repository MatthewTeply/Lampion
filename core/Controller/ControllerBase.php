<?php

namespace Lampion\Controller;

abstract class ControllerBase {

    /**
     * @param array $params
     * @return mixed
     */
    abstract public function index($params);

    /**
     * Returns an instance of ControllerLoader, so ControllerBase can load models, other controller, etc.
     * @return ControllerLoader
     */
    public function load() {
        return new ControllerLoader();
    }
}