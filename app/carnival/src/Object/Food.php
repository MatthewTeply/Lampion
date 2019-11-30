<?php
namespace Carnival\Object;

use Lampion\Database\ORM;

class Food extends ORM
{
    public $name;
    public $price;

    public function __construct($id = null) {
        $this->initORM($id);
    }

    public function save() {
        $this->saveORM();
    }
}