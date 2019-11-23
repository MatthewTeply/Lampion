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
    public function initORM($id, string $table = null, array $columns = []) {
        $this->id = $id;

        # If table remains empty, it is presumed that table name is the same as class name
        if($table === null)
            $table = strtolower(get_called_class());

        $this->db['table'] = $table;

        # If columns remain empty, it is presumed that var names and table column names are the same
        if(empty($columns)) {
            foreach (get_object_vars($this) as $key => $var) {
                if($key != self::$dbVarName && $key != "id") {
                    $columns[$key] = $key;
                }
            }
        }

        if($this->id !== null) {
            $person = $this->selectQuery($this->db['table'], ["*"], [
                "where" => "id=$this->id"
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
    public function saveORM(array $columns = []) {
        # If columns remain empty, it is presumed that var names and table column names are the same
        # If the columns are empty and the id is null, that means we want to create a new entry with table column names same as variable names
        if(empty($columns)) {
            foreach (get_object_vars($this) as $key => $var) {
                if($key != self::$dbVarName && $key != "id") {
                    $columns[$key] = $var;
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
            $this->updateQuery($this->db['table'], $columns, ["where" => "id=$this->id"]);
        }

        # If row does not exist in DB, insert it
        else {
            $this->id = $this->insertQuery($this->db['table'], $columns);
        }
    }

    public function deleteORM() {
        $this->deleteQuery($this->db['table'], ["id" => "=$this->id"]);
    }
}