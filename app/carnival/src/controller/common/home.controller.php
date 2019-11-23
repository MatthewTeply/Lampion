<?php

use Lampion\Controller\ControllerBase;

class ControllerCommonHome extends ControllerBase
{
    public function index() {
        $view = $this->load()->view();

        $view->header = $view->load("partials/header");
        $view->footer = $view->load("partials/footer");

        $view->firstName = "Matyáš";
        $view->lastName  = "Teplý";

        $view->render("common/home");
    }
}