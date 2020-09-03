<?php

namespace Lampion\Http;

use Lampion\Form\FormHandler;
use Lampion\Http\Response;
use Lampion\Http\Request;
use Lampion\Http\Url;
use Lampion\Core\Runtime;

use stdClass;

/**
 * Router class
 * @todo Better FormHandler registration
 * @author Matyáš Teplý
 */
class Router
{
    public static $get    = array();
    public static $post   = array();
    public static $put    = array();
    public static $delete = array();

    public $listening;

    public function __construct() {
        $this->listening = false;
    }

    protected static function processURL($request_method, $request_method_args = null) {
        $url_explode = explode('/', $_GET['url']); # Get url divided by slashes

        foreach($request_method as $route) {
            $path = $route['path']; # Route's path (string)

            if(sizeof(explode('/', $path)) == sizeof($url_explode)) { # Find a route that matches the length of URL
                $args = array(); # Initialize array containing arguments for given page

                foreach(explode('/', $path) as $key => $field) {
                    if(strpos($field, '{') !== false && strpos($field, '}') !== false) { # If field contains { and }, it is an argument
                        array_push($args, [
                            'pos'  => $key, # Save argument's position
                            'name' => substr($field, 1, -1) # Save argument's name
                        ]);
                    }
                }

                $path_new = explode('/', $path); # Initialize new path, containing path exploded by slashes
                $args_new = new stdClass(); # Initialize object, that is going to contain params with values under an index that is argument's name in request method's array

                foreach($args as $arg) {
                    $path_new[$arg['pos']]  = $url_explode[$arg['pos']]; # Composing new path

                    $args_new->{$arg['name']} = $url_explode[$arg['pos']]; # Adding params
                }

                $path_new = implode('/', $path_new); # Glue pieces together with slashes

                if($path_new == $_GET['url']) { # If new path is equal to current URL, execute callback
                    if(!is_string($route['callback'])) { # If callback is not short method
                        $route['callback'](new Request($args_new), new Response); # Execute callback with request and respone as params
                    }

                    else {
                        $args_new->req = new Request($args_new);
                        $args_new->res = new Response;

                        self::shortMethod($route['callback'], $args_new); # Else call short method
                    }

                    return;
                }
            }
        }

        if(!empty(HTTP_NOT_FOUND_REDIR)) {
            die(HTTP_NOT_FOUND_REDIR);
            //Url::redirect(HTTP_NOT_FOUND_REDIR);
        }

        http_response_code(404);
        die(HTTP_NOT_FOUND);
    }

    protected static function shortMethod($callback, $args = null) {
        if(is_string($callback)) {
            $callback = explode('::', $callback);

            $response = new Response();
            $request  = new Request();

            $path   = $callback[0];
            $method = $callback[1];

            if(!class_exists($path)) {
                $response->json([
                    'error' => 'Route does not exist!'
                ]);

                return;
            }

            $class = new $path;

            $class->$method($request, $response);
        }
    }

    public function get(string $path, $callback) {
        self::$get[$path] = [
            'path'     => $path,
            'callback' => $callback
        ];

        return $this;
    }

    public function post(string $path, $callback) {
        self::$post[$path] = [
            'path'     => $path,
            'callback' => $callback
        ];

        return $this;
    }

    public function put(string $path, $callback) {
        self::$put[$path] = [
            'path'     => $path,
            'callback' => $callback
        ];

        return $this;
    }

    public function delete(string $path, $callback) {
        self::$delete[$path] = [
            'path'     => $path,
            'callback' => $callback
        ];

        return $this;
    }

    public function redirect(string $to) {
        Url::redirect($to);
    }

    public function listen() {
        $request_method = strtolower($_SERVER['REQUEST_METHOD'] ?? 'get'); # Get request methdos, and convert it to lowercase
        $_GET['url'] = rtrim($_GET['url'], '/'); # Trim trailing slashes

        $this->listening = true;

        $fh = new FormHandler;
        $fh->registerRoutes($this);

        switch($request_method) {
            case 'get':
                self::processURL(self::$get);
                break;
            case 'post':
                self::processURL(self::$post, $_POST);
                break;
            case 'put':
                self::processURL(self::$put, $_GET);
                break;
            case 'delete':
                self::processURL(self::$delete, $_GET);
                break;
            default:
                http_response_code(404);
                die(HTTP_NOT_FOUND);
                break;
        }
    }

    public function __destruct() {
        if(!$this->listening) {
            Runtime::error(1);
        }
    }
}