<?php
$app = new \Lampion\Application\Application();

$app->router->get("home", "\Carnival\Controller\Common\DashboardController::index");

$app->router->listen();