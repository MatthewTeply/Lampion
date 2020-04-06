<?php
require_once 'Core/Autoloader.php';
require_once 'vendor/autoload.php';
require_once 'Misc/decodeUrl.php';

# Initialize dotenv
$dotenv = \Dotenv\Dotenv::create(CONFIG);
$dotenv->load();

# Database
define('DB_HOST', getenv("DB_HOST"));
define('DB_NAME', getenv("DB_NAME"));
define('DB_PASS', getenv("DB_PASS"));
define('DB_USER', getenv("DB_USER"));

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
