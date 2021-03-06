<?php

require_once 'config/config.php';
require_once 'config/config.defaults.php';
require_once 'Core/Autoloader.php';
require_once 'vendor/autoload.php';
require_once 'Misc/decodeUrl.php';

# Initializing default session variables
$_SESSION['Lampion']['app']              = $app;
$_SESSION['Lampion']['DB']['queryCount'] = 0;
$_SESSION['Lampion']['DB']['queries']    = [];
$_SESSION['Lampion']['scripts']          = [];
$_SESSION['Lampion']['language']         = !isset($_SESSION['Lampion']['language']) ? DEFAULT_LANGUAGE : $_SESSION['Lampion']['language'];

# Initializing global variables
$_ERRORS = [];

\Lampion\Core\Autoloader::register();
\Lampion\Core\Autoloader::registerShutdownFunctions();

\Lampion\Application\ApplicationManager::init();
\Lampion\Database\Initializer::checkKernelTables();
