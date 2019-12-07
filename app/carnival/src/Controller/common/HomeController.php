<?php
namespace Carnival\Controller\Common;

use Carnival\Entity\Food;
use Lampion\Controller\ControllerBase;
use Carnival\Model\FoodModel;
use Lampion\Cookie\Cookie;
use Lampion\User\User;

class HomeController extends ControllerBase
{
    public function index() {
        $view = $this->load()->view();
        $viewTest = $this->load("test")->view();

        $data['food'] = FoodModel::getFood(14);

        $food = new Food();

        $data['menu'] = FoodModel::getFoodAll();

        $view->render("common/home", $data);
    }
}