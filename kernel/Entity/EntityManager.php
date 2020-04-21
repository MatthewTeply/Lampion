<?php

namespace Lampion\Entity;

use Lampion\Database\Query;
use ReflectionClass;
use stdClass;

class EntityManager {

    private function getTableName(string $entity) {
        $table = explode('\\', $entity);
        $table = strtolower(end($table));

        return Query::tableExists($table) ? $table : 'entity_' . $table;
    }

    public function persist(object $entity) {
        $vars  = get_object_vars($entity);
        $table = $this->getTableName(get_class($entity));

        if(!Query::tableExists($table)) {
            return false;
        }

        # Check if var has a setter, if it does, use it and set it in the $vars array
        foreach($vars as $key => $var) {
            $methodName = 'set' . ucfirst($key);

            if(method_exists($entity, $methodName)) {
                $entity->$methodName($var);

                $vars[$key] = $entity->$key;
            }
        }

        # Creating new row
        if(!$vars['id']) {
            Query::insert($table, $vars);
        }

        # Updating row
        else {
            Query::update($table, $vars, [
                'id' => $vars['id']
            ]);
        }

        return true;
    }

    public function find(string $entityName, int $id) {
        $table = $this->getTableName($entityName);

        $fields = Query::select($table, ['*'], [
            'id' => $id
        ])[0];

        if(!isset($fields['id'])) {
            return false;
        }

        $entity = new $entityName();

        foreach($fields as $key => $field) {
            $entity->$key = $field;
        }

        return $entity;
    }

    public function findBy(string $entityName, array $searchFields) {
        $table = $this->getTableName($entityName);

        $fields = Query::select($table, ['*'], $searchFields)[0];

        if(!isset($fields['id'])) {
            return false;
        }

        $entity = new $entityName();

        foreach($fields as $key => $field) {
            $entity->$key = $field;
        }

        return $entity;
    }

    public function all(string $entityName) {
        $table = $this->getTableName($entityName);
        $ids   = Query::select($table, ['id']);

        $entities = [];

        foreach($ids as $id) {
            $entities[] = $this->find($entityName, $id);
        }

        return $entities;
    }

    public function destroy(object $entity) {
        if(!$entity->id) {
            return false;
        }

        Query::delete($this->getTableName(get_class($entity)), ['id' => $entity->id]);

        return true;
    }

    public function metadata(string $entityName) {
        if(!class_exists($entityName)) {
            return;
        }

        $entity = new ReflectionClass($entityName);
    
        $properties = new stdClass();

        foreach($entity->getProperties() as $property) {
            $docComment = $property->getDocComment();

            if($docComment == '') {
                continue;
            }
            
            preg_match('/@var\((.*?)\)/', $docComment, $varParams);
            $varParams = explode(',', $varParams[1]);

            foreach($varParams as $varParam) {
                preg_match('/(.*?)="(.*?)"/', $varParam, $param);

                // WARNING: Creating default object from empty value
                // NOTE: Not sure what is causing this, works for now
                @$properties->{$property->getName()}->{trim($param[1])} = $param[2];
            }
        }

        return $properties;
    }

}