<?php

# Kernel paths
define('ROOT', getcwd() . "/");
@define('WEB_ROOT', $_SERVER['HTTP_HOST'] == "localhost" ? "http://" . $_SERVER['HTTP_HOST'] . "/" . explode("htdocs" . DIRECTORY_SEPARATOR, dirname(__DIR__))[1] . "/" : $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/lampion/');

define('KERNEL', "kernel/");
define('APP', "app/");
define('CONFIG', "config/");
define('TOOLS', 'tools/');

define('KERNEL_VAR', 'var/');
define('KERNEL_SQL', KERNEL_VAR . 'sql/');

define('KERNEL_USR', 'usr/');

define('KERNEL_PUBLIC', 'public/');
define('KERNEL_TEMPLATES', KERNEL_PUBLIC . 'templates/');
define('KERNEL_ASSETS', KERNEL_PUBLIC . 'assets/');

# App paths
define('SRC', "/src/");
define('CONTROLLERS', "/src/Controller/");
define('MODELS', "/src/Model/");
define('ENTITY', "/src/Entity/");
define('TEMPLATES', "/public/templates/");

define('ASSETS', "/public/assets/");
define('CSS', ASSETS . "css/");
define('IMG', ASSETS . "images/");
define('SCRIPTS', ASSETS . "scripts/");

define('APP_VAR', "/var/");
define('LANG', APP_VAR . "lang/");
define('SQL', APP_VAR . "sql/");
define('STORAGE', APP_VAR . "storage/");

define('USR', '/usr/');

# Loader paths
define('PLUGINS', "plugins/");

# Files
define('MAX_FILESIZE', 1000000000000);

# HTTP codes
define('HTTP_NOT_FOUND', 404);
define('HTTP_FORBIDDEN', 403);
define('HTTP_UNAUTHORIZED', 401);
define('HTTP_INTERNAL_ERROR', 500);
define('HTTP_OK', 200);

# Error redirects
define('HTTP_NOT_FOUND_REDIR', '404');
define('HTTP_FORBIDDEN_REDIR', '403');

# Database
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'carnival');
define('DB_USER', 'admin');
define('DB_PASS', '3Q7rf$%v@J58');
define('DB_PORT', 3306);

# Authentication
define('JWT_SECRET_KEY', 'BD72A6978E58B7F7F4785C65472354ACFE1FC40F546C5C96EA30F32706CD1241');

define('ENVIRONMENT', 'dev');