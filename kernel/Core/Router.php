<?php

namespace Lampion\Core;

use Lampion\Debug\Console;
use Lampion\Form\FormHandler;
use Lampion\Http\Response;
use Lampion\Http\Request;
use Lampion\Http\Url;
use Lampion\User\Auth;

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
        /*
        if($request_method_args) { # If method arguments are already set, don't get arguments from URL
            ldm($request_method_args);

            if(!is_string($request_method[$_GET['url']]['callback'])) {
                $request_method[$_GET['url']]['callback'](new Request($request_method_args), new Response);
            }

            else {
                self::short_method($request_method[$_GET['url']]['callback'], [
                    'req' => new Request($request_method_args),
                    'res' => new Response
                ]);
            }
            
            return;
        }

        else {
            
        }
        */

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
                    if(!is_string($route['callback'])) { # If callback is not short method
                        $route['callback'](new Request($args_new), new Response); # Execute callback with request and respone as params
                    }

                    else {

                        $args_new['req'] = new Request($args_new);
                        $args_new['res'] = new Response;

                        self::short_method($route['callback'], $args_new); # Else call short method
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

    protected static function short_method($callback, $args = []) {
        if(is_string($callback)) {
            $callback = explode("::", $callback);

            $_GET['Request'] = $args['req'];
            $_GET['Response'] = $args['res'];

            $path   = $callback[0];
            $method = $callback[1];

            $class = new $path;

            $class->$method();
        }
    }

    public function get(string $path, $callback) {
        self::$get[$path] = [
            "path"     => $path,
            "callback" => $callback
        ];

        return $this;
    }

    public function post(string $path, $callback) {
        self::$post[$path] = [
            "path"     => $path,
            "callback" => $callback
        ];

        return $this;
    }

    public function put(string $path, $callback) {
        self::$put[$path] = [
            "path"     => $path,
            "callback" => $callback
        ];

        return $this;
    }

    public function delete(string $path, $callback) {
        self::$delete[$path] = [
            "path"     => $path,
            "callback" => $callback
        ];

        return $this;
    }

    public function redirect(string $to) {
        Url::redirect($to);
    }

    public function listen() {
        $request_method = strtolower($_SERVER['REQUEST_METHOD']); # Get request methdos, and convert it to lowercase
        $_GET['url'] = rtrim($_GET['url'], '/'); # Trim trailing slashes

        $this->listening = true;

        $this->post('form', function(Request $req, Response $res) {
            $fh = new FormHandler();

            $post = null;

            foreach($_POST as $key => $data) {
                if(isset($data['value']) && isset($data['type'])) {
                    $post[$key] = $fh->handle($data['type'], $data['value']);
    
                }
            }

            $post['authToken'] = Cookie::get('lampionToken') ?? null;

            // TODO: $_FILES handling

            $useragent = $_SERVER['HTTP_USER_AGENT'];
            $ckfile = tempnam ("/tmp", "CURLCOOKIE");
           
            session_write_close();

            $ch = curl_init($_POST['action']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_COOKIESESSION, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            curl_setopt($ch,CURLOPT_USERAGENT, $useragent);
            curl_setopt ($ch, CURLOPT_COOKIEFILE, $ckfile);

            $response = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_close($ch);

            // TODO: Response check
            if($httpcode == 302)  {
                // Success
            }

            else {
                // Fail
            }
            
            //Url::redirect();

            /*
            echo 'Loading...<br>';
            echo '<form id="redir-form" action="' . $_POST['action'] . '" method="POST">';
            foreach($_POST as $key => $data) {
                if(isset($data['value']) && isset($data['type'])) {
                    echo '<input type="hidden" name="' . $key . '" value="' . htmlspecialchars($fh->handle($data['type'], $data['value'])) . '">';
                }
            }

            foreach($_FILES as $key => $data) {
                echo '<input type="hidden" name="' . $key . '" value="' . htmlspecialchars($fh->handle($key, $data)) . '">';
            }
            echo '<noscript>';
            echo 'This function requires JavaScript to be enabled, please enable JavaScript in your browser\'s settings.';
            echo '</noscript>';
            echo '</form>';
            echo '<script>document.getElementById("redir-form").submit();</script>';
            */
        });

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