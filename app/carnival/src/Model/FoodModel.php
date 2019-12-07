<?php
namespace Carnival\Model;

use Carnival\Entity\Food;
use Lampion\Database\Query;

class FoodModel
{
    public static function getFood($id) {
        $food = new Food($id);

        return [
            'name'  => $food->name,
            'price' => $food->price
        ];
    }

    public function getFoodAll() {
        return Query::selectQuery("food", ['*']);
    }
}