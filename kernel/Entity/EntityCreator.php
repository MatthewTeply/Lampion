<?php

namespace Lampion\Entity;

use Exception;
use Lampion\Application\Application;
use Lampion\Database\Query;
use Lampion\FileSystem\FileSystem;

class EntityCreator {

    public function create(array $config) {
        $entityName = array_key_first($config);
        $fields     = $config[$entityName]['fields'];

        $this->createConfig($config);
        $this->createTable($entityName, $fields);
        $this->createEntity($entityName, $fields);

        return true;
    }

    private function createEntity($entityName, $fields) {
        $appName = ucfirst(Application::name());

        # Initializing entity
        $code = <<<CODE
<?php

namespace $appName\\Entity;

class $entityName {

    /** @var(type="int", length="11") */
    public \$id;
CODE;

        $code .= PHP_EOL . PHP_EOL;
        
        # Declaring variables
        $fieldsCode = [];

        foreach($fields as $key => $field) {
            $fieldCode     = '';
            $metadataCode  = '/** @var(';

            # Metadata var type
            $metadataCode .= 'type="' . $field['type'] . '"';

            # Attaching metadata
            foreach($field['metadata'] as $metadataKey => $value) {

                # Convert bool value to string
                if(is_bool($value)) {
                    $value = $value ? 'true' : 'false';
                }

                $metadataCode .= ' ' . $metadataKey . '="' . $value . '"';
            }

            $metadataCode .= ')';

            # Putting the variable together
            $fieldCode .= "\t" . $metadataCode . ' */' . PHP_EOL;
            $fieldCode .= "\t" . 'public $' . $key . ';' . PHP_EOL;

            $fieldsCode[] = $fieldCode;
        }

        $code .= implode(PHP_EOL, $fieldsCode);
        $code .= PHP_EOL . '}';

        $fs = new FileSystem(ROOT . APP . Application::name() . ENTITY);

        $fs->write($entityName . '.php', $code, 0777);
    }

    private function createTable($entityName, $fields) {
        $tableName = 'entity_' . strtolower($entityName);
        $cols      = [];

        $cols[] = 'id int(11) not null PRIMARY KEY AUTO_INCREMENT';

        foreach($fields as $key => $field) {
            # Transform entity and file type to int(11), beacuse we need the referenced entity's ID
            if($field['type'] == 'entity' || $field['type'] == 'file') {
                $field['type'] = 'JSON';
            }

            if(isset($field['metadata']['nullable'])) {
                $field['metadata']['nullable'] = $field['metadata']['nullable'] == 'true' ? true : false;
            }

            $cols[] = ($field['metadata']['mappedBy'] ?? $key) . ' ' 
            . $field['type'] 
            . (isset($field['metadata']['length']) ? '(' . $field['metadata']['length'] . ')' : '')
            . (!$field['metadata']['nullable'] ? ' not null ' : ' null') 
            . ($field['db']['options'] ?? '');
        }

        $colsQuery = implode(',', $cols);

        $query  = 'CREATE TABLE ' . $tableName . ' (';
        $query .= $colsQuery;
        $query .= ') CHAR SET \'utf8mb4\'';

        try {
            return Query::raw($query);
        }

        catch(Exception $e) {
            return $e;
        }
    }

    public function createConfig(array $config) {
        $entityName = array_key_first($config);
        $data       = $config[$entityName]['general'];

        $json = [
            $entityName => []
        ];

        foreach($data as $key => $value) {
            $json[$entityName][$key] = $value;
        }

        $fs = new FileSystem(ROOT . APP . Application::name() . '/' . CONFIG . 'carnival/admin/entity/');

        $fs->write($entityName . '.json', json_encode($json), 0777);
    }

}