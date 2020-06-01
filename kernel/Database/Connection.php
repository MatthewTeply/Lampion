<?php

namespace Lampion\Database;

/**
 * Class that takes care of connecting to the database
 * @author Matyáš Teplý
 */
class Connection
{
    static protected $dbhost = DB_HOST;
    static protected $dbname = DB_NAME;
    static protected $dbuser = DB_USER;
    static protected $dbpass = DB_PASS;
    static protected $dbport = DB_PORT;

    /**
     * Connects to a database
     * @return PDO
     */
    public static function connect() {
        try {
            $pdo = new \PDO("mysql:host=".self::$dbhost.";dbname=".self::$dbname.";charset=utf8mb4;port=" . self::$dbport, self::$dbuser, self::$dbpass);
        }

        catch (\PDOException $pdoE) {
            echo 'An Error occurred: ' . $pdoE->getMessage();
        }

        return $pdo;
    }
}