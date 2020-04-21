<?php

namespace Lampion\Form;

use Lampion\Application\Application;

class FormHandler {

    public function handle(string $field, $data) {
        // Default field's controller
        $className = 'Lampion\\Form\\Field\\' . $field . 'FormField';
        $class     = null;

        // Check if field's controller is overwritten in current app, or if a custom one is specified
        if(class_exists(ucfirst(Application::name()) . '\Form\Field\\' . $field . 'FormField')) {
            $className = ucfirst(Application::name()) . '\Form\Field\\' . $field . 'FormField';
        }

        if(class_exists($className)) {
            $class = new $className();

            return $class->submit($data);
        }

        else {
            return $data;
        }
    }

}