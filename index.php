<?php

error_reporting(E_ALL);
ini_set('display_errors', 'on');

session_start();
$rustart = getrusage();

unset($_SESSION['Lampion']['queryCount']);

# Configs
require_once 'config/config.php';
require_once 'config/config.defaults.php';

# Loading all necessary classes
require_once 'vendor/autoload.php';
require_once 'kernel/Bootstrap.php';

$ru = getrusage();
\Lampion\Core\Runtime::rutime($ru, $rustart, "utime");
\Lampion\Core\Runtime::rutime($ru, $rustart, "stime");
