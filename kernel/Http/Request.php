<?php

namespace Lampion\Http;

class Request
{
    public $params;

    public function __construct($params) {
        $this->params = $params;
    }

    public static function isAjax() {
        if(isset($_POST['lampionIsAjaxRequest'])) {
            return true;
        }

        if(empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower(@$_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {    
            return false;
        }

        else {
            return true;
        }
    }
}