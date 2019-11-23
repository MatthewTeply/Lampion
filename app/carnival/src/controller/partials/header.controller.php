<?php

use Lampion\Controller\ControllerBase;

class ControllerPartialsHeader extends  ControllerBase
{
    public function index()
    {
        $view = $this->load()->view();

        $view->url = $_GET['url'];

        $view->render("partials/header");
    }
}