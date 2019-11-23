<?php

namespace Lampion\Database;

class Connection
{
    static protected $dbhost = "localhost";//DB_HOST;
    static protected $dbname = "lampion";//DB_NAME;
    static protected $dbuser = "root";//DB_USER;
    static protected $dbpass = "";//DB_PASS;

    /**
     * Connects to a database
     * @return PDO
     */
    public static function connect() {
        $pdo = new \PDO("mysql:host=".self::$dbhost.";dbname=".self::$dbname.";charset=utf8mb4", self::$dbuser, self::$dbpass);
        return $pdo;
    }
}