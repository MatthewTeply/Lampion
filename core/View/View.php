<?php

namespace Lampion\View;

use Lampion\Controller\ControllerLoader;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use Twig\Markup;

class View {

    private $twig;

    /**
     * View constructor.
     * @param $templateFolder
     */
    public function __construct($templateFolder)
    {
        $loader = new FilesystemLoader($templateFolder);
        $this->twig = new Environment($loader);
    }

    /**
     * Renders a template
     * @param string $path
     * @param array $args
     * @return $this
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function render(string $path, array $args = []) {
        echo $this->twig->render("$path.twig", !empty($args) ? $args : get_object_vars($this));

        return $this;
    }

    /**
     * Loads a template, if a controller with the same directory path is found, it is used to render the template with variables inserted
     * @param string $path
     * @param array $args
     * @param bool $rawTemplate
     * @return Markup
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function load(string $path, array $args = [], bool $rawTemplate = false) {
        $loader = new ControllerLoader();

        if($loader->controller($path) && !$rawTemplate) {
            ob_start();
                echo $loader->controller($path)->index();
            return new Markup(ob_get_clean(), 'UTF-8');
        }

        else {
            return new Markup($this->twig->render("$path.twig", $args), 'UTF-8');
        }
    }

}