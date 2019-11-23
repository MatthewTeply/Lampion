<?php

use Lampion\Controller\ControllerBase;

class ControllerCommonHome extends ControllerBase
{
    public function index() {
        $view = $this->load()->view();

        $data['header'] = $view->load("partials/header");
        $data['footer'] = $view->load("partials/footer");

        $data['firstName'] = "Matyáš";
        $data['lastName']  = "Teplý";

        $view->render("common/home", $data);
    }
}