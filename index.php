<?php
session_start();

# Configs
require_once 'config/config.php';
require_once 'config/config.defaults.php';

# Loading all necessary classes
require_once  'vendor/autoload.php';
require_once 'core/Bootstrap.php';

# Initializing Router and ApplicationManager
$router = new \Lampion\Core\Router();
$appManager = new Lampion\Application\ApplicationManager();