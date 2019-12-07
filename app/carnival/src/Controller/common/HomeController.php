<?php
namespace Carnival\Controller\Common;

use Carnival\Entity\Food;
use Lampion\Controller\ControllerBase;
use Carnival\Model\FoodModel;
use Lampion\User\User;

class HomeController extends ControllerBase
{
    public function index() {
        $view = $this->load()->view();
        $viewTest = $this->load("test")->view();

        $data['header'] = $view->load("partials/header");
        $data['footer'] = $view->load("partials/footer");

        $data['food'] = FoodModel::getFood(14);

        $food = new Food();

        $data['menu'] = FoodModel::getFoodAll();

        $data['viewTest'] = $viewTest->load("hello");

        $user = new User(1);

        echo $user->username;

        $view->render("common/home", $data);
    }
}