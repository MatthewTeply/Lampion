<?php

require_once 'Core/Autoloader.php';

$dotenv = \Dotenv\Dotenv::create(".");
$dotenv->load();

# Sorting out URL and app
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

# Initializing default session variables
$_SESSION['Lampion']['app']              = $app;
$_SESSION['Lampion']['DB']['queryCount'] = 0;
$_SESSION['Lampion']['DB']['queries']    = [];
$_SESSION['Lampion']['scripts']          = [];

# Initializing global variables
$_ERRORS = [];

\Lampion\Core\Autoloader::register();
\Lampion\Core\Autoloader::registerPluginFunctions();
\Lampion\Core\Autoloader::registerShutdownFunctions();

\Lampion\Application\ApplicationManager::init();
\Lampion\Database\Initializer::checkKernelTables();
