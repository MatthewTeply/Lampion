<?php
namespace Carnival\Controller;

use Carnival\Object\Food;
use Lampion\Controller\ControllerBase;
use Carnival\Model\FoodModel;

class HomeController extends ControllerBase
{
    public function index() {
        $view = $this->load()->view();

        $data['header'] = $view->load("partials/header");
        $data['footer'] = $view->load("partials/footer");

        $data['food'] = FoodModel::getFood(14);

        $data['menu'] = FoodModel::getFoodAll();

        $view->render("common/home", $data);
    }
}