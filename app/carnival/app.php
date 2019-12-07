<?php
$app = new \Lampion\Application\Application();

$app->router->get("home", "\Carnival\Controller\Common\HomeController::index");

$app->router->listen();