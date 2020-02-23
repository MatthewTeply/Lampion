<?php

require_once 'Core/Autoloader.php';
require_once 'vendor/autoload.php';
require_once 'Misc/decodeUrl.php';

# Initialize dotenv
$dotenv = \Dotenv\Dotenv::create(".");
$dotenv->load();

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
