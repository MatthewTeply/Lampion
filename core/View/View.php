<?php

namespace Lampion\View;

use Twig\Loader\FilesystemLoader;
use Twig\Environment;

class View {

    public function render(string $template) {
        $loader = new FilesystemLoader(CORE);
        $twig = new Environment($loader);

        echo $twig->render("$template.twig", get_object_vars($this));
    }

}