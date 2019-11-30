<?php

# Core framework paths
define('ROOT', getcwd() . "/");
define('WEB_ROOT', $_SERVER['HTTP_HOST'] . "/");

define('CORE', ROOT . "core/");
define('APP', ROOT . "app/");
define('CONFIG', ROOT . "config/");

# App paths
define('SRC', APP . $_SESSION['lampionApp'] . "/src/");
define('CONTROLLER', APP . $_SESSION['lampionApp'] . "/src/controller/");
define('MODEL', APP . $_SESSION['lampionApp'] . "/src/model/");
define('OBJECT', APP . $_SESSION['lampionApp'] . "/src/object/");
define('TEMPLATE', APP . $_SESSION['lampionApp'] . "/public/template/");
define('ASSETS', APP . $_SESSION['lampionApp'] . "/public/assets/");
define('DATA', APP . $_SESSION['lampionApp'] . "/data/");
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