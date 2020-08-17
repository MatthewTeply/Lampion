<?php

namespace Lampion\Http;

use stdClass;

/**
 * Class containing all info about HTTP/HTTPS request
 * @author Matyáš Teplý
 */
class Request
{
    public function __construct($params = null) {
        if($params) {
            foreach($params as $key => $param) {
                $this->{$key} = $param;
            }
        }
    }

    public function query(string $key, $value = null) {
        if($value) {
            return $_GET[$key] = $value;
        }

        return $_GET[$key] ?? null;
    }

    public function hasQuery(string $key) {
        return isset($_GET[$key]);
    }

    public function input(string $key, $value = null) {
        if($value) {
            return $_POST[$key] = $value;
        }

        return $_POST[$key] ?? null;
    }

    public function hasInput(string $key) {
        return isset($_POST[$key]);
    }

    public function file(string $key) {
        return $_FILES[$key] ?? null;
    }

    public function hasFile(string $key) {
        return isset($_FILES[$key]);
    }

    public function all() {
        $returnObj = new stdClass();

        $returnObj->get   = (object)$_GET;
        $returnObj->post  = (object)$_POST;
        $returnObj->files = (object)$_FILES;

        return $returnObj;
    }

    public function url() {
        return $_GET['url'];
    }

    public function urlBase() {
        return explode('/', $_GET['url'])[0];
    }

    public function method() {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    public function isMethod(string $method) {
        return strtolower($_SERVER['REQUEST_METHOD']) == $method;
    }

    public function referer() {
        return $_SERVER['HTTP_REFERER'] ?? null;
    }

    public function isAjax() {
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