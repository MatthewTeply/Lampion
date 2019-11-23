<?php
session_start();

# Configs
require_once 'config/config.php';
require_once 'config/config.defaults.php';

# Loading all necessary classes
require_once 'vendor/autoload.php';
require_once 'core/Bootstrap.php';

# Initializing Router and ApplicationManager
$router = new \Lampion\Core\Router();

$app = new \Lampion\Application\Application();

$app->router->get("home", function (\Lampion\Http\Request $req, \Lampion\Http\Response $res) {
    $res->controller("common/home")->index();
});

$app->router->init();