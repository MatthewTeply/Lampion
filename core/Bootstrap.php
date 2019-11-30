<?php

require_once 'Core/Autoloader.php';

if(isset($_GET['url'])) {
    $firstUrlParam = explode("/", $_GET['url'])[0];

    if(is_dir(APP . $firstUrlParam)) {
        $app = $firstUrlParam;

        if(is_array(explode($firstUrlParam . "/", $_GET['url'])) > 1)
            $_GET['url'] = explode($firstUrlParam . "/", $_GET['url'])[1];
    }

    else {
        $app = DEFAULT_APP;
    }
}

else {
    $app = DEFAULT_APP;
}

$_SESSION['lampionApp'] = $app;

\Lampion\Core\Autoloader::register();
\Lampion\Application\ApplicationManager::init();