<?php

namespace Lampion\Entity;

use Carnival\Entity\Order;
use Lampion\Database\Query;
use Lampion\Debug\Console;
use Lampion\FileSystem\Entity\Dir;
use Lampion\FileSystem\Entity\File;
use Lampion\Misc\Util;
use Lampion\User\Entity\User;
use ReflectionClass;
use stdClass;

/**
 * Class that takes care of entity persistance
 * @author Matyáš Teplý
 */
class EntityManager {

    private function getTableName(string $entity) {
        $table = explode('\\', $entity);
        $table = strtolower(preg_replace(['/([a-z\d])([A-Z])/', '/([^_])([A-Z][a-z])/'], '$1_$2', end($table)));

        return 'entity_' . $table;
    }

    public function persist(object $entity) {
        $table    = $this->getTableName(get_class($entity));
        $metadata = $this->metadata(get_class($entity));

        $entityFormer = $entity->id ? $this->find(get_class($entity), $entity->id) : null;

        $files = [];

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

            # Entity/File handling
            if(isset($metadata->{$key})) {
                if($metadata->{$key}->type == 'entity' || $metadata->{$key}->type == 'file') {
                    $json = [];
                    
                    if($var === null) {
                        $var = '[]';
                    }

                    if(Util::validateJson($var)) {
                        $var = json_decode($var);
                    }

                    if(!is_array($var)) {
                        $var = [$var];
                    }

                    # Creating JSON of entity references
                    foreach($var as $entityReferenceKey => $entityReference) {
                        $json[$entityReferenceKey] = is_object($entityReference) ? $entityReference->id : $entityReference;

                        if($metadata->{$key}->type == 'file') {
                            $files[$entityReferenceKey]['entity_name'] = get_class($entity);
                            $files[$entityReferenceKey]['file_id']     = $json[$entityReferenceKey];
                            $files[$entityReferenceKey]['property']    = $key;
                        }
                    }
    
                    $entity->{$key} = json_encode($json);
                }
    
                # Change property name to table column named that the property is mapped to
                if(isset($metadata->{$key}->mappedBy) && $metadata->{$key}->mappedBy != $key) {
                    $entity->{$metadata->{$key}->mappedBy} = $entity->{$key};
                    unset($entity->{$key});
                }
            }
        }

        // TODO: Better default language implementation
        # Insert default language if language is not specified
        if(Query::isColumn($table, 'meta_lang')) {
            $entity->meta_lang = $entity->meta_lang ?? '["1"]'; 
        }

        $entityArray = (array)$entity;

        # Creating new row
        if(!$entityArray['id'] || $entityArray['id'] === null) {
            Query::insert($table, $entityArray);
        }

        # Updating row
        else {
            Query::update($table, $entityArray, [
                'id' => $entityArray['id']
            ]);
        }

        # If entity's ID is not set, it has to be the last ID inserted
        if(!isset($entityArray['id'])) {
            $entityArray['id'] = Query::select($table, ['id'], [], 'id', 'DESC')[0]['id'];
        }

        # Handling file uses
        foreach($files as $file) {
            $file['entity_id'] = $entityArray['id'];

            $uses = Query::select('file_uses', ['*'], [
                'entity_name' => $file['entity_name'],
                'entity_id'   => $file['entity_id'],
                'property'    => $file['property']
            ], null, null, false);

            # If ID is 0, that means it is being cleared
            if($file['file_id'] != 0) {
                if(empty($uses[0])) {
                    Query::insert('file_uses', $file, false);
                }

                else {
                    Query::update('file_uses', [
                        'file_id' => $file['file_id']
                    ], [
                        'id' => $uses[0]['id']
                    ], false);
                }
            }
        }

        return true;
    }

    public function find(string $entityName, $id, $sortBy = null, $sortOrder = null, $log = true) {
        $ids = [];
        
        if(Util::validateJson($id)) {
            $jsonIds = json_decode($id);

            if(is_iterable($jsonIds)) {
                foreach($jsonIds as $value) {
                    $ids[] = $value;
                }
            }
        }

        $table = $this->getTableName($entityName);

        if($entityName == File::class || $entityName == Dir::class || User::class) {
            $log = false;
        }

        if(empty($ids)) {
            $fields = Query::select($table, ['*'], [
                'id' => $id
            ], $sortBy, $sortOrder, $log)[0];
    
            if(!isset($fields['id'])) {
                return false;
            }

            $entity = new $entityName();

            $this->setFields($entity, $fields);
        }

        else {
            $entities = [];
            
            foreach($ids as $id) {
                $fields = Query::select($table, ['*'], [
                    'id' => $id
                ], $sortBy, $sortOrder, $log)[0];
        
                if(!isset($fields['id'])) {
                    continue;
                }

                $entity = new $entityName();
                $entities[] = $this->setFields($entity, $fields);
            }

            if(empty($entities)) {
                return false;
            }
        }

        if(isset($entities) && sizeof($entities) == 1) {
            $entities = $entities[0];
        }

        return !empty($entities) ? $entities : $entity;
    }

    public function findBy(string $entityName, array $searchFields, $sortBy = null, $sortOrder = null, $log = true) {
        $table = $this->getTableName($entityName);

        $searchFields = $this->transformFieldsToColumns($entityName, $searchFields);

        if($entityName == File::class || $entityName == Dir::class || $entityName == User::class) {
            $log = false;
        }

        $results = Query::select($table, ['*'], $searchFields, $sortBy, $sortOrder, $log);

        foreach($results as $key => $result) {
            if(!isset($result['id'])) {
                unset($results[$key]);
            }
        }

        if(sizeof($results) == 0) {
            return false;
        }

        $entities = [];

        foreach($results as $fields) {
            $entity = new $entityName();
    
            $this->setFields($entity, $fields);

            $entities[] = $entity;
        }

        return $entities;
    }

    public function all(string $entityName, $sortBy = null, $sortOrder = null) {
        $table = $this->getTableName($entityName);
        $ids   = Query::select($table, ['id'], [], $sortBy, $sortOrder);

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

        # Delete all file uses
        $fileUses = Query::select('file_uses', ['id'], [
            'entity_name' => get_class($entity),
            'entity_id'   => $entity->id
        ], null, null, false);

        if(!empty($fileUses[0])) {
            foreach($fileUses as $fileUse) {
                Query::delete('file_uses', [
                    'id' => $fileUse['id']
                ], false);
            }
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
                foreach(explode(' ', $varParam) as $varParamExplode) {
                    preg_match('/(.*?)="(.*?)"/', $varParamExplode, $param);
    
                    if(!isset($param[1])) {
                        continue;
                    }
    
                    // WARNING: Creating default object from empty value
                    // NOTE: Not sure what is causing this, works for now
                    @$properties->{$property->getName()}->{trim($param[1])} = $param[2];
                }
            }
        }

        return $properties;
    }

    public function isMetaField(string $fieldName) {
        $metaKeyword = 'meta_';

        return substr($fieldName, 0, strlen($metaKeyword)) == $metaKeyword;
    }

    public function lastId(string $entityName) {
        return Query::select($this->getTableName($entityName), ['id'], [], 'id', 'DESC')[0]['id'];
    }

    private function setFields(object &$entity, $fields) {
        $metadata = $this->metadata(get_class($entity));

        # Check if entity has a getter declared, if so, use it (LEGACY)
        foreach($fields as $key => $value) {
            //$getMethod = 'get' . ucfirst($key);

            $entity->{$key} = $value;

            /*
            if(method_exists($entity, $getMethod)) {
                $entity->{$key} = $entity->$getMethod();
            }
            */
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

        # Check if entity property is a refference to another class, if it is, 
        # populate the property with the said class, also set metadata object
        foreach($entity as $key => $value) {
        
            # Check if entity property is a reference to a file
            if(isset($metadata->{$key}) && $metadata->{$key}->type == 'file') {
                $entity->{$key} = [];
                
                foreach(json_decode($value) as $fileId) {
                    $entity->{$key}[] = $this->find('Lampion\\FileSystem\\Entity\\File', $fileId);
                }

                if(sizeof($entity->{$key}) == 1) {
                    $entity->{$key} = $entity->{$key}[0];
                }
            }

            # Check if entity property is a reference to a directory
            if(isset($metadata->{$key}) && $metadata->{$key}->type == 'dir') {
                $entity->{$key} = $this->find('Lampion\\FileSystem\\Entity\\Dir', $value);
            }

            if(isset($metadata->{$key}->entity)) {
                $entity->{$key} = [];

                if(is_string($value)) {
                    $entityIds = json_decode($value);
                }

                else {
                    $entityIds = json_decode(is_array($value) ? $value[0] : $value);
                }

                if(is_iterable($entityIds)) {
                    foreach($entityIds as $entityId) {
                        $entity->{$key}[] = $this->find($metadata->{$key}->entity, $entityId);
                    }
    
                    if(sizeof($entity->{$key}) == 1) {
                        $entity->{$key} = $entity->{$key}[0];
                    }
                }

                // TODO: Think of something better!
                # Assign key to metadata object, if column starts with $metaKeyword
                if($this->isMetaField($metadata->{$key}->mapped_by ?? $key)) {
                    if(!isset($entity->_metafields)) {
                        $entity->_metafields = new stdClass();
                    }
                    
                    $entity->_metafields->{$key} = $entity->{$key};
                    unset($entity->{$key});
                }
            }

            /*
            $getMethod = 'get' . ucfirst($key);

            if(method_exists($entity, $getMethod)) {
                $entity->{$key} = $entity->$getMethod();
            }
            */
        }

        if(get_class($entity) == Order::class) {
            //var_dump($entity);
        }
    }

    private function transformFieldsToColumns(string $entityName, array $fields) {
        $metadata = $this->metadata($entityName);

        foreach($fields as $key => $field) {
            # Get field's metadata
            $fieldMetadata = $metadata->{$key} ?? null;

            # If metadata for current field don't exist, skip cycle
            if(!$fieldMetadata) {
                continue;
            }

            # Check if field is mapped to a specific DB column
            $fieldName  = $fieldMetadata->mappedBy ?? $key;

            $fieldValue = null;

            # Transform field's value here
            switch($fieldMetadata->type) {
                case 'entity':
                    $fieldValue = '["' . $field->id . '"]';
                    break;
                default:
                    $fieldValue = $field;
                    break;
            }

            # If field is mapped to a column, unset the original
            if($fieldName != $key) {
                unset($fields[$key]);
            }

            $fields[$fieldName] = $fieldValue;
        }

        return $fields;
    }
}