<?php

namespace Lampion\Entity;

use Lampion\Database\Query;

use Lampion\Debug\Console;

abstract class Entity
{
    /**
     * @param $id
     */
    abstract public function __construct($id = null);

    /**
     * Method for saving entity, persisting it
     */
    abstract public function persist();

    /**
     * Method for destroying entity, deleting it
     */
    abstract public function destroy();

    static $dbVarName = 'db';

    // Public:
    public $id;

    // Private:
    private $db;

    /**
     * Initializes ORM values for classes that inherit this class
     * @param int $id
     * @param string $table
     * @param array $columns
     * @return bool
     */
    protected function init($id, string $table = null, array $columns = []) {
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
        
        # Check if table with entity's name exists, if not try pluralizing it, if that doesn't exist, return false
        if(!Query::tableExists($this->db['table'])) {
            $this->db['table'] = 'entity_' . $this->db['table'];

            if(!Query::tableExists($this->db['table'])) {
                return false;
            }
        }

        # If columns remain empty, it is presumed that var names and table column names are the same
        if(empty($columns)) {
            foreach (get_object_vars($this) as $key => $var) {
                if($key != self::$dbVarName && $key != "id") {
                    if(Query::isColumn($this->db['table'], $key)) {
                        $columns[$key] = $key;
                    }
                }
            }
        }

        # If id is specified, insert table values into entity's variables
        if($this->id !== null) {
            $tableVals = Query::select($this->db['table'], ["*"], [
                "id" => $this->id
            ]);

            if(empty($tableVals[0])) {
                return false;
            }

            foreach ($columns as $key => $column) {
                $this->$column = $tableVals[0][$key];
            }
            
        }
    }

    /**
     * 'Saves' ORM values, either inserts them if ID is no set, or updates if it is
     * @param array $columns
     * @return bool
     */
    protected function save(array $columns = []) {
        # If columns remain empty, it is presumed that var names and table column names are the same
        # If the columns are empty and the id is null, that means we want to create a new entry with table column names same as variable names
        if(empty($columns)) {
            foreach (get_object_vars($this) as $key => $var) {
                if($key != self::$dbVarName && $key != "id") {
                    if(Query::isColumn($this->db['table'], $key)) {
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

        # Before entering row into DB, check if entity has a setter for each parameter, if it has, use it
        foreach($columns as $key => $column) {
            $methodName = 'set' . ucfirst($key);

            if(method_exists($this, $methodName)) {
                $this->$methodName($column);
            }
        }

        # If row exists in DB, update it
        if($this->id !== null) {
            Query::update($this->db['table'], $columns, [
                'id' => ["=", $this->id]
            ]);

            return true;
        }

        # If row does not exist in DB, insert it
        else {
            $this->id = Query::insert($this->db['table'], $columns);

            return true;
        }
    }

    protected function delete() {
        if($this->id === null)
            return false;

        Query::delete($this->db['table'], ["id" => ["=", $this->id]]);

        return true;
    }

    public function test() {
        $methodName = 'setPassword';

        Console::log(method_exists($this, $methodName));
    }
}