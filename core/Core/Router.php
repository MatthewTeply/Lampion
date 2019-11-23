<?php

namespace Lampion\Core;

use Lampion\Http\Response;
use Lampion\Http\Request;

use Lampion\Core\Session;

class Router
{
    public $get;
    public $post;
    public $put;
    public $delete;

    public function __construct() {
        $this->get    = array();
        $this->post   = array();
        $this->put    = array();
        $this->delete = array();

        //$this->url = new URL;
        //$this->logs = new Logs;
    }

    /**
     * Processes URL, separates parameters from URL
     * @param $request_method
     * @param null $request_method_args
     */
    protected function processURL($request_method, $request_method_args = null) {
        if($request_method_args) { # If method arguments are already set, don't get arguments from URL
            $request_method[$_GET['url']]['callback'](new Request($request_method_args), new Response);
            return;
        }

        else { # If arguments are not set, get them from URL
            $url_explode = explode("/", $_GET['url']); # Get url divided by slashes

            foreach($request_method as $route) {
                $path = $route['path']; # Route's path (string)

                if(sizeof(explode("/", $path)) == sizeof($url_explode)) { # Find a route that matches the length of URL
                    $args = array(); # Initialize array containing arguments for given page

                    foreach(explode("/", $path) as $key => $field) {
                        if(strpos($field, "{") !== false && strpos($field, "}") !== false) { # If field contains { and }, it is an argument
                            array_push($args, [
                                "pos" => $key, # Save argument's position
                                "name" => substr($field, 1, -1) # Save argument's name
                            ]);
                        }
                    }

                    $path_new = explode("/", $path); # Initialize new path, containing path exploded by slashes
                    $args_new = array(); # Initialize array, that is going to contain arguments with values under an index that is argument's name in request method's array

                    foreach($args as $arg) {
                        $path_new[$arg['pos']] = $url_explode[$arg['pos']]; # Composing new path
                        $args_new[$arg['name']] = $url_explode[$arg['pos']]; # Adding arguments
                    }

                    $path_new = implode("/", $path_new); # Glue pieces together with slashes

                    if($path_new == $_GET['url']) { # If new path is equal to current URL, execute callback
                        //if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') # Check if request is not ajax
                        //    $this->logs->set($this->url->getClientIp() . " – – [" . date("d/m/Y H:i:s") . "] \"" . strtoupper($_SERVER['REQUEST_METHOD']) . " " . $_GET['app'] . "/$path_new\"", "access"); # Set log

                        $route['callback'](new Request($args_new), new Response);
                        return;
                    }
                }
            }
        }

        if(!empty(HTTP_NOT_FOUND_REDIR))
            //$this->url->redirect(HTTP_NOT_FOUND_REDIR);

        die(HTTP_NOT_FOUND);
    }

    /**
     * @param string $path
     * @param $callback
     */
    public function get(string $path, $callback) {
        $this->get[$path] = [
            "path"     => $path,
            "callback" => $callback
        ];
    }

    /**
     * @param string $path
     * @param $callback
     */
    public function post(string $path, $callback) {
        $this->post[$path] = [
            "path" => $path,
            "callback" => $callback
        ];
    }

    /**
     * @param string $path
     * @param $callback
     */
    public function put(string $path, $callback) {
        $this->put[$path] = [
            "path"     => $path,
            "callback" => $callback
        ];
    }

    /**
     * @param string $path
     * @param $callback
     */
    public function delete(string $path, $callback) {
        $this->delete[$path] = [
            "path" => $path,
            "callback" => $callback
        ];
    }

    public function redirect(string $to) {
        //header("Location:" . $this->url->link($to));
    }

    /**
     * Initiates route
     */
    public function listen() {
        $request_method = strtolower($_SERVER['REQUEST_METHOD']); # Get request method, and convert it to lowercase
        $_GET['url'] = rtrim($_GET['url'], '/'); # Trim trailing slashes

        switch($request_method) {
            case "get":
                $this->processURL($this->get);
                break;
            case "post":
                $this->processURL($this->post, $_POST);
                break;
            case "put":
                $this->processURL($this->put, $_GET);
                break;
            case "delete":
                $this->processURL($this->delete, $_GET);
                break;
            default:
                die(HTTP_NOT_FOUND);
                break;
        }
    }
}