<?php

namespace Lampion\Entity;

use Lampion\Database\Query;
use Lampion\Debug\Console;
use ReflectionClass;
use stdClass;

class EntityManager {

    private function getTableName(string $entity) {
        $table = explode('\\', $entity);
        $table = strtolower(end($table));

        return Query::tableExists($table) ? $table : 'entity_' . $table;
    }

    public function persist(object $entity) {
        $table    = $this->getTableName(get_class($entity));
        $metadata = $this->metadata(get_class($entity));

        $entityFormer = $entity->id ? $this->find(get_class($entity), $entity->id) : null;

        if(!Query::tableExists($table)) {
            return false;
        }

        foreach($entity as $key => $var) {
            # Check if var has a setter, if it does, use it and set it in the $vars array
            $methodName = 'set' . ucfirst($key);

            if(method_exists($entity, $methodName)) {

                # If method doesn't return anything, it is presumed that value is not supposed to change
                if(empty($entity->$methodName($var))) {
                    $entity->{$key} = $entityFormer->{$key} ?? $entity->{$key};
                }

                # If method returns data, set it
                else {
                    $entity->{$key} = $entity->$methodName($var);
                }
            }

            # Fill mapping column with their property's value, and unset the property
            if(isset($metadata->{$key}->mappedBy)) {
                $entity->{$metadata->{$key}->mappedBy} = $var;
                unset($entity->{$key});
            }
        }

        $entity = (array)$entity;

        # Creating new row
        if(!$entity['id']) {
            Query::insert($table, $entity);
        }

        # Updating row
        else {
            Query::update($table, $entity, [
                'id' => $entity['id']
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

        $this->setFields($entity, $fields);

        return $entity;
    }

    public function findBy(string $entityName, array $searchFields) {
        $table = $this->getTableName($entityName);

        $fields = Query::select($table, ['*'], $searchFields)[0];

        if(!isset($fields['id'])) {
            return false;
        }

        $entity = new $entityName();

        $this->setFields($entity, $fields);

        return $entity;
    }

    public function all(string $entityName) {
        $table = $this->getTableName($entityName);
        $ids   = Query::select($table, ['id']);

        $entities = [];

        if(empty($ids[0])) {
            return false;
        }

        foreach($ids as $id) {
            $entities[] = $this->find($entityName, $id['id']);
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

    private function setFields(object &$entity, $fields) {
        $metadata = $this->metadata(get_class($entity));

        # Check if entity has a getter declared, if so, use it
        foreach($fields as $key => $value) {
            $getMethod = 'get' . ucfirst($key);

            $entity->{$key} = $value;

            if(method_exists($entity, $getMethod)) {
                $entity->{$key} = $entity->$getMethod();
            }
        }

        # Get differenece between columns and metadata
        $diff = array_diff(array_keys((array)$metadata), array_keys($fields));

        # Populate property fields by their mapping column's value, and unset the colummn
        foreach($diff as $field) {
            if(isset($metadata->{$field}->mappedBy)) {
                $entity->{$field} = $entity->{$metadata->{$field}->mappedBy};
                unset($entity->{$metadata->{$field}->mappedBy});
            }
        }

        # Check if entity property is a refference to another class, if it is, populate the property with the said class
        foreach($entity as $key => $value) {
            if(isset($metadata->{$key}->entity)) {
                $entity->{$key} = $this->find($metadata->{$key}->entity, $value);
            }
        }
    }
}