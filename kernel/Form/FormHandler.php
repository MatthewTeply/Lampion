<?php

namespace Lampion\Form;

use Error;
use Lampion\Application\Application;
use Lampion\FileSystem\FileSystem;

class FormHandler {

    public function handle(string $field, $data) {
        // Default field's controller
        $className = 'Lampion\\Form\\Field\\' . $field . 'FormField';
        $class     = null;

        // Check if field's controller is overwritten in current app, or if a custom one is specified
        if(class_exists(ucfirst(Application::name()) . '\Form\Field\\' . ucfirst($field) . 'FormField')) {
            $className = ucfirst(Application::name()) . '\Form\Field\\' . ucfirst($field) . 'FormField';
        }

        if(class_exists($className)) {
            $class = new $className();

            return $class->submit($data);
        }

        else {
            # Handling files
            if(isset($data['tmp_name'])) {
                foreach($data as $key => $value) {
                    $data[$key] = $value['value'];
                } 

                $fs = new FileSystem();

                try {
                    return $fs->upload($data, '');
                }

                catch(Error $e) {
                    return $e;
                }
            }

            return $data;
        }
    }

}