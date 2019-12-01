<?php
$app = new \Lampion\Application\Application();

$app->router->get("home", function (\Lampion\Http\Request $req, \Lampion\Http\Response $res) {
    $res->controller("common/home")->index();

    $test = new \Test\Controller\Common\TestController();
    $test->index();
});

$app->router->listen();