<?php

namespace Lampion\Core;

use http\Url;
use Lampion\Http\Response;
use Lampion\Http\Request;

use Lampion\Core\Session;

class Router
{
    public static $get    = array();
    public static $post   = array();
    public static $put    = array();
    public static $delete = array();

    protected static function processURL($request_method, $request_method_args = null) {
        if($request_method_args) { # If method arguments are already set, don't get arguments from URL
            $request_method[$_GET['url']]['callback'](new Request($request_method_args), new Response);
            return;
        }

        else { # If arguments are not set, get them from URL
            $url_explode = explode("/", $_GET['url']); # Get url divided by slashes

            foreach($request_method as $route) {
                $path = $route['path']; # Route's path (string)

                if(sizeof(explode("/", $path)) == sizeof($url_explode)) { # Find a route that matches the length of URL
                    $args = array(); # Initialize array containing arguments for given page

                    foreach(explode("/", $path) as $key => $field) {
                        if(strpos($field, "{") !== false && strpos($field, "}") !== false) { # If field contains { and }, it is an argument
                            array_push($args, [
                                "pos"  => $key, # Save argument's position
                                "name" => substr($field, 1, -1) # Save argument's name
                            ]);
                        }
                    }

                    $path_new = explode("/", $path); # Initialize new path, containing path exploded by slashes
                    $args_new = array(); # Initialize array, that is going to contain arguments with values under an index that is argument's name in request method's array

                    foreach($args as $arg) {
                        $path_new[$arg['pos']]  = $url_explode[$arg['pos']]; # Composing new path
                        $args_new[$arg['name']] = $url_explode[$arg['pos']]; # Adding arguments
                    }

                    $path_new = implode("/", $path_new); # Glue pieces together with slashes

                    if($path_new == $_GET['url']) { # If new path is equal to current URL, execute callback
                        if(!is_string($route['callback'])) # If callback is not short method
                            $route['callback'](new Request($args_new), new Response); # Execute callback with request and respone as params
                        else
                            self::short_method($route['callback'], $args_new); # Else call short method

                        return;
                    }
                }
            }
        }

        if(!empty(HTTP_NOT_FOUND))
            Url::redirect(HTTP_NOT_FOUND_REDIR);

        die(HTTP_NOT_FOUND);
    }

    protected static function short_method($callback, $params) {
        if(is_string($callback)) {
            $callback = explode("::", $callback);

            $path   = $callback[0];
            $method = $callback[1];

            $class = new $path;
            $class->$method();
        }
    }

    public static function get(string $path, $callback) {
        self::$get[$path] = [
            "path"     => $path,
            "callback" => $callback
        ];
    }

    public static function post(string $path, $callback) {
        self::$post[$path] = [
            "path" => $path,
            "callback" => $callback
        ];
    }

    public static function put(string $path, $callback) {
        self::$put[$path] = [
            "path"     => $path,
            "callback" => $callback
        ];
    }

    public static function delete(string $path, $callback) {
        self::$delete[$path] = [
            "path" => $path,
            "callback" => $callback
        ];
    }

    public static function redirect(string $to) {
        header("Location:" . self::$url->link($to));
    }

    public static function listen() {
        $request_method = strtolower($_SERVER['REQUEST_METHOD']); # Get request methdos, and convert it to lowercase
        $_GET['url'] = rtrim($_GET['url'], '/'); # Trim trailing slashes

        switch($request_method) {
            case "get":
                self::processURL(self::$get);
                break;
            case "post":
                self::processURL(self::$post, $_POST);
                break;
            case "put":
                self::processURL(self::$put, $_GET);
                break;
            case "delete":
                self::processURL(self::$delete, $_GET);
                break;
            default:
                die(ERR_NOT_FOUND);
                break;
        }
    }
}