<?php

# Core framework paths
define('ROOT', getcwd() . "/");
define('WEB_ROOT', $_SERVER['HTTP_HOST'] . "/");

define('CORE', ROOT . "core/");
define('APP', ROOT . "app/");
define('CONFIG', ROOT . "config/");

# HTTP codes
define('HTTP_NOT_FOUND', 404);
define('HTTP_FORBIDDEN', 403);
define('HTTP_UNAUTHORIZED', 401);
define('HTTP_INTERNAL_ERROR', 500);
define('HTTP_OK', 200);

# Error redirects
define('HTTP_NOT_FOUND_REDIR', '404');
define('HTTP_FORBIDDEN_REDIR', '403');