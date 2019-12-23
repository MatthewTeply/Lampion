<?php

require "src/ConsoleInput.php";

require "Objects/Controller.php";

$ci = new ConsoleInput();

$ci->read($argv);

echo $ci->type . "\n";
echo $ci->app . "\n";
echo $ci->path . "\n";
print_r($ci->flags); echo "\n";

switch ($ci->type) {
    case "controller":
        $controller = new Controller($ci->app, $ci->path);

        $controller->generate();

        break;
    default:
        break;
}