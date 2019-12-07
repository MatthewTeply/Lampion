<?php

# Core framework paths
define('ROOT', getcwd() . "/");
define('WEB_ROOT', $_SERVER['HTTP_HOST'] . "/");

define('CORE', ROOT . "core/");
define('APP', ROOT . "app/");
define('CONFIG', ROOT . "config/");

# App paths
define('SRC', APP . $_SESSION['Lampion']['app'] . "/src/");
define('CONTROLLERS', APP . $_SESSION['Lampion']['app'] . "/src/Controller/");
define('MODELS', APP . $_SESSION['Lampion']['app'] . "/src/Model/");
define('OBJECTS', APP . $_SESSION['Lampion']['app'] . "/src/Entity/");
define('TEMPLATES', APP . $_SESSION['Lampion']['app'] . "/public/templates/");

define('ASSETS', APP . $_SESSION['Lampion']['app'] . "/public/assets/");
define('CSS', ASSETS . "css/");
define('IMG', ASSETS . "images/");
define('SCRIPTS', ASSETS . "scripts/");

define('DATA', APP . $_SESSION['Lampion']['app'] . "/data/");
define('LANGUAGE', DATA . "language/");
define('SQL', DATA . "sql/");
define('STORAGE', DATA . "storage/");

# HTTP codes
define('HTTP_NOT_FOUND', 404);
define('HTTP_FORBIDDEN', 403);
define('HTTP_UNAUTHORIZED', 401);
define('HTTP_INTERNAL_ERROR', 500);
define('HTTP_OK', 200);

# Error redirects
define('HTTP_NOT_FOUND_REDIR', '404');
define('HTTP_FORBIDDEN_REDIR', '403');