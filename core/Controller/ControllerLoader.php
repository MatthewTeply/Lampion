<?php

namespace Lampion\Controller;

class ControllerLoader
{
    public function model(string $path) {
        echo "Loaded model: $path.model.php";
    }

    public function controller(string $path) {}
    public function language(string $path) {}
}