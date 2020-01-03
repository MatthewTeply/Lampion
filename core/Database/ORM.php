<?php

namespace Lampion\Database;

abstract class ORM extends Query
{
    /**
     * ORM constructor.
     * @param $id
     */
    abstract public function __construct($id = null);

    /**
     * Save method, where the saveORM method is going to be used
     * @return mixed
     */
    abstract public function save();

    static $dbVarName = 'db';

    public $id;
    private $db;

    /**
     * Initializes ORM values for classes that inherit this class
     * @param int $id
     * @param string $table
     * @param array $columns
     */
    protected function initORM($id, string $table = null, array $columns = []) {
        $this->id = $id;

        # If table remains empty, it is presumed that table name is the same as class name
        if($table === null) {
            if(strpos(get_called_class(), "\\") !== false) {
                $table = explode("\\", strtolower(get_called_class()));
                $table = end($table);
            }

            else {
                $table = strtolower(get_called_class());
            }
        }

        $this->db['table'] = $table;

        # If columns remain empty, it is presumed that var names and table column names are the same
        if(empty($columns)) {
            foreach (get_object_vars($this) as $key => $var) {
                if($key != self::$dbVarName && $key != "id") {
                    if(self::isColumn($table, $key)) {
                        $columns[$key] = $key;
                    }
                }
            }
        }

        if($this->id !== null) {
            $person = Query::selectQuery($this->db['table'], ["*"], [
                "id" => ["=", $this->id]
            ]);

            foreach ($columns as $key => $column) {
                $this->$column = $person[$key];
            }
        }
    }

    /**
     * 'Saves' ORM values, either inserts them if ID is no set, or updates if it is
     * @param array $columns
     */
    protected function saveORM(array $columns = []) {
        # If columns remain empty, it is presumed that var names and table column names are the same
        # If the columns are empty and the id is null, that means we want to create a new entry with table column names same as variable names
        if(empty($columns)) {
            foreach (get_object_vars($this) as $key => $var) {
                if($key != self::$dbVarName && $key != "id") {
                    if(self::isColumn($this->db['table'], $key)) {
                        if(!empty($var)) {
                            $columns[$key] = $var;
                        }
                    }
                }
            }
        }

        # If ID is null, that means the record does not yet exist, so it has to be created
        # This is used for when id is null and the table columns are specified
        if(!empty($column) && $this->id === null) {
            foreach ($columns as $key => $column) {
                $columns[$key] = $column;
            }
        }

        # If row exists in DB, update it
        if($this->id !== null) {
            Query::updateQuery($this->db['table'], $columns, [
                'id' => ["=", $this->id]
            ]);
        }

        # If row does not exist in DB, insert it
        else {
            $this->id = Query::insertQuery($this->db['table'], $columns);
        }
    }

    protected function deleteORM() {
        Query::deleteQuery($this->db['table'], ["id" => ["=", $this->id]]);
    }
}