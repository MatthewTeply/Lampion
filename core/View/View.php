<?php

namespace Lampion\View;

use Lampion\Controller\ControllerLoader;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use Twig\Markup;

class View {

    private $twig;

    public function __construct($templateFolder)
    {
        $loader = new FilesystemLoader($templateFolder);
        $this->twig = new Environment($loader);
    }

    public function render(string $path) {
        echo $this->twig->render("$path.twig", get_object_vars($this));

        return $this;
    }

    public function load(string $path, bool $rawTemplate = false) {
        $loader = new ControllerLoader();

        if($loader->controller($path) && !$rawTemplate) {
            ob_start();
                echo $loader->controller($path)->index();
            return new Markup(ob_get_clean(), 'UTF-8');
        }

        else {
            return new Markup($this->twig->render("$path.twig", get_object_vars($this)), 'UTF-8');
        }
    }

}