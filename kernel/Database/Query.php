<?php

namespace Lampion\Database;

use Lampion\Core\Runtime;
use Lampion\Debug\Error;

class Query extends Connection
{
    /**
     * Performs a raw PDO query
     * @param string $query
     * @param array  $params
     * @param bool   $escape
     * @param bool   $report_err
     * @return mixed
     */
    public static function raw(string $query, array $params = [], $escape = true, $report_err = true) {
        $pdo = self::connect();

        $stmnt = $pdo->prepare($query);

        if(!$stmnt && $report_err) {
            Error::set('DB error: ' . $pdo->errorInfo());
            //Runtime::setDbInfo($query, $params, $pdo->errorInfo(), "Error");
            exit();
        }

        if($escape) {
            foreach($params as $key => $param) {
                $params[$key] = htmlspecialchars($param);

                if(is_numeric($param))
                    $params[$key] = $param + 0;
            }
        }

        $queryStartTime = round(microtime(true) * 1000, 2);

        $stmnt->execute($params);

        $queryEndTime = round(microtime(true) * 1000, 2);

        $warningsStmnt = $pdo->query('SHOW WARNINGS');
        $warnings = $warningsStmnt->fetchAll();

        $queryInfo['query'] = $query;
        $queryInfo['execTime'] = $queryEndTime - $queryStartTime;
        $queryInfo['timestamp'] = date('H:i:s');

        $result = $stmnt->fetchAll(\PDO::FETCH_ASSOC);

        if(empty($warnings)) {
            Runtime::setDbInfo($queryInfo, $params, $result,"Query was successful", "Success", 0);
        }
        else {
            $warnings = $warnings[0];

            Runtime::setDbInfo($queryInfo, $params, null, $warnings['Message'], $warnings['Level'], $warnings['Code']);
        }

        $queryType = strtolower(explode(" ", $query)[0]);

        if($queryType == "select") {
            if($result == null)
                return [$result]; # Return var in array, only containing null in the position 0

            return $result;
        }

        if($queryType == "insert") {
            return (int)$pdo->lastInsertId();
        }

        return $result;
    }

    /**
     * Processes conditions
     * @param array   $conditions
     * @return string $conditionsString
     */
    private static function processConditions(array $conditions) {
        $conditionsString = "";

        if(sizeof($conditions) == 0)
            return;

        $conditionsString .= " WHERE ";

        $i = 0;
        foreach ($conditions as $key => $condition) {
            if($i != 0 && isset($condition[2])) {
                $conditionsString .= " $condition[2] ";
            }

            else if($i != 0 && !isset($condition[2])) {
                $conditionsString .= " AND ";
            }

            $conditionsString .= $key;
            $conditionsString .= " $condition[0] ";
            $conditionsString .= ":$key";

            $i++;
        }

        /*
        foreach ($conditions as $key => $condition) {
            $conditionsString .= " " . strtoupper($key) . " " . $condition;
        }
        */

        return $conditionsString;
    }

    /**
     * Processes array of conditions into a string, that is readable by SQL
     * @param array $conditions
     */
    public static function processParams(array $conditions) {
        $params = [];

        foreach ($conditions as $key => $condition) {
            $params[":$key"] = is_array($condition) ? $condition[1] : $condition;
        }

        return $params;
    }

    /**
     * Performs an INSERT query
     * @param string $table
     * @param array  $columns
     * @return int   $last_insert_id
     */
    public static function insert(string $table, array $columns) {
        $insert = "INSERT INTO " . $table . " (" . implode(",", array_keys($columns)) . ") ";
        $values = "VALUES (";

        foreach (array_keys($columns) as $key => $column) {
            $values .= ":$column";

            if($key != sizeof($columns) - 1)
                $values .= ",";
        }

        $values .= ")";

        return (int)self::raw($insert . $values, $columns);
    }

    /**
     * Performs a SELECT query
     * @param string $table
     * @param array  $columns
     * @param array  $conditions
     * @param Query  $instance
     * @return array|mixed
     */
    public static function select(string $table, array $columns, array $conditions = []) {
        $data = self::raw("SELECT " . implode(",", $columns) . " FROM " . $table . self::processConditions($conditions), self::processParams($conditions));

        if(sizeof($data) == 0)
            return [];
        else
            return $data;
    }

    /**
     * Performs a DELETE query
     * @param string $table
     * @param array  $conditions
     */
    public static function delete(string $table, array $conditions) {
        self::raw("DELETE FROM " . $table . self::processConditions($conditions), self::processParams($conditions));
    }

    /**
     * Performs an UPDATE query
     * @param string $table
     * @param array  $columns
     * @param array  $conditions
     */
    public static function update(string $table, array $columns, array $conditions) {
        $columnsString = "";

        foreach (array_keys($columns) as $key) {
            if(!empty($columnsString))
                $columnsString .= ", $key = :$key";
            else
                $columnsString .= " SET $key = :$key";
        }

        $conditionsArray = [];

        $conditionsArray = array_merge($conditionsArray, self::processParams($columns));
        $conditionsArray = array_merge($conditionsArray, self::processParams($conditions));

        self::raw("UPDATE " . $table . $columnsString . self::processConditions($conditions), $conditionsArray);
    }

    public static function isColumn(string $table, string $column) {
        $result = self::raw("SHOW COLUMNS FROM `$table` LIKE '$column'");

        if(is_array($result))
            return sizeof($result) != 0 ? true : false;
        elseif(is_bool($result))
            return $result;
    }

    public static function tableExists(string $tableName) {
        return self::raw("DESCRIBE `$tableName`") ?? false;
    }
}
