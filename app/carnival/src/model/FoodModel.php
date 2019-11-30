<?php
namespace Carnival\Model;

use Carnival\Object\Food;
use Lampion\Database\Query;

class FoodModel extends Query
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