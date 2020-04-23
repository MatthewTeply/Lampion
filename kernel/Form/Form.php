<?php

namespace Lampion\Form;

use Lampion\Application\Application;
use Lampion\Debug\Console;
use Lampion\View\View;

class Form {

    public $action;
    public $method;
    public $fields = [];

    protected $view;

    public function __construct(string $action, string $method) {
        $this->action = $action;
        $this->method = $method;

        $this->view = new View(KERNEL_TEMPLATES, '');
    }

    public function field(string $type, array $options, $path = 'form/field/') {
        // NOTE: @ is here because undefined constant in FormDefaultFields is handled by the ?? operator
        $inputType = @constant('Lampion\Form\FormDefaultFields::' . strtoupper($type)) ?? constant('Lampion\Form\FormDefaultFields::STRING');
        $fieldController = ucfirst(Application::name()) . '\\Form\\Field\\' . ucfirst($type) . 'FormField';

        if(class_exists($fieldController)) {
            $fieldController = new $fieldController();
            
            if(method_exists($fieldController, 'display')) {
                if($fieldController->display($options)) {
                    $template = $fieldController->display($options);
                }
            }
        }

        else {
            $template = $this->view->load($path . $inputType['field'], $options, true);
        }

        if(!$inputType) {
            // TODO: Error handling
            return false;
        }

        if(!isset($options['type'])) {
            $options['type'] = $options['type'] ?? $type;
        }

        $this->fields[$options['name']]['template'] = $template;
        $this->fields[$options['name']]['type']     = $options['type'];
        $this->fields[$options['name']]['name']     = $options['name'];
        $this->fields[$options['name']]['attr']     = $options['attr'] ?? null;

        return $this;
    }

    public function render() {
        $this->view->render('form/base', [
            'action' => $this->action,
            'method' => $this->method,
            'fields' => $this->fields
        ]);
    }

}