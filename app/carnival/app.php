<?php
$app = new \Lampion\Application\Application();

$app->router->get("home", function (\Lampion\Http\Request $req, \Lampion\Http\Response $res) {
    $res->controller("common/home")->index();
});

$app->router->listen();