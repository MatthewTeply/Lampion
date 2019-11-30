<?php

namespace Lampion\Database;

class Query extends Connection
{
    /**
     * Performs a raw PDO query
     * @param string $query
     * @param array $params
     * @param bool $escape
     * @param bool $report_err
     * @return array
     */
    public static function rawQuery(string $query, array $params = [], $escape = true, $report_err = true) {
        $pdo = self::connect();
        if($report_err) $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING );

        $stmnt = $pdo->prepare($query);

        if(!$stmnt && $report_err) {
            throw new Exception('DB error: ' . $pdo->errorInfo());
        }

        if($escape) {
            foreach($params as $key => $param) {
                $params[$key] = htmlspecialchars($param);
            }
        }

        $stmnt->execute($params);

        $queryType = strtolower(explode(" ", $query)[0]);

        if($queryType == "select") {
            $data = $stmnt->fetchAll(\PDO::FETCH_ASSOC);

            if($data == null)
                return [$data]; # Return data in array, only containing null in the position 0

            return $data;
        }

        if($queryType == "insert") {
            return (int)$pdo->lastInsertId();
        }
    }

    /**
     * Processes conditions
     * @param array $conditions
     * @return string $conditionsString
     */
    private static function processConditions(array $conditions) {
        $conditionsString = "";

        foreach ($conditions as $key => $condition) {
            $conditionsString .= " " . strtoupper($key) . " " . $condition;
        }

        return $conditionsString;
    }

    /**
     * Performs an INSERT query
     * @param string $table
     * @param array $columns
     * @return int $last_insert_id
     */
    public static function insertQuery(string $table, array $columns) {
        # Looping through all columns, adding quotes to strings
        foreach ($columns as $key => $column) {
            if(is_string($column)) {
                $columns[$key] = "'" . $column . "'";
            }
        }

        try {
            return (int)self::rawQuery("INSERT INTO " . $table . " (" . implode(",", array_keys($columns)) . ") VALUES (" . implode(",", $columns) . ")");
        }

        catch (\Exception $e) {
            echo $e;
        }
    }

    /**
     * Performs a SELECT query
     * @param string $table
     * @param array $columns
     * @param array $conditions
     * @param Query $instance
     * @return array|mixed
     */
    public static function selectQuery(string $table, array $columns, array $conditions = []) {
        try {
            $data = self::rawQuery("SELECT " . implode(",", $columns) . " FROM " . $table . Query::processConditions($conditions));
        }

        catch (\Exception $e) {
            echo $e;
            return;
        }

        if(sizeof($data) == 0)
            return [];
        elseif(sizeof($data) == 1) {
            $keys = array_keys($data[0]);

            if(sizeof($keys) > 1)
                return $data[0];
            else
                return $data[0][$keys[0]];
        }
        else
            return $data;
    }

    /**
     * Performs a DELETE query
     * @param string $table
     * @param array $conditions
     */
    public static function deleteQuery(string $table, array $conditions) {
        try {
            self::rawQuery("DELETE FROM " . $table . " WHERE " . Query::processConditions($conditions));
        }

        catch (\Exception $e) {
            echo $e;
        }
    }

    /**
     * Performs an UPDATE query
     * @param string $table
     * @param array $columns
     * @param array $conditions
     */
    public static function updateQuery(string $table, array $columns, array $conditions) {
        $columnsString = "";

        foreach ($columns as $key => $column) {
            if(!empty($columnsString))
                $columnsString .= ", $key='$column'";
            else
                $columnsString .= " SET $key='$column'";
        }

        try {
            self::rawQuery("UPDATE " . $table . $columnsString . Query::processConditions($conditions));
        }

        catch (\Exception $e) {
            echo $e;
        }
    }
}