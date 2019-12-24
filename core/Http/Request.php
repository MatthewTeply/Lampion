<?php

namespace Lampion\Http;

class Request
{
    public $params;

    public function __construct($params) {
        $this->params = $params;
    }
}