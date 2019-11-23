<?php

# Core framework paths
define('ROOT', getcwd() . "/");
define('WEB_ROOT', $_SERVER['HTTP_HOST'] . "/");

define('CORE', ROOT . "core/");
define('APP', ROOT . "app/");
define('CONFIG', ROOT . "config/");

# App paths
define('CONTROLLER', "src/controller/");
define('MODEL', "src/model/");
define('TEMPLATE', "public/template/");
define('ASSETS', "public/assets/");
define('DATA', "data/");
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