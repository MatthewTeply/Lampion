<?php

namespace Lampion\Form;

use Lampion\View\View;

class Form {

    public $action;
    public $method;
    public $fields;

    protected $view;

    public function __construct(string $action, string $method) {
        $this->action = $action;
        $this->method = $method;

        $this->view = new View(KERNEL_TEMPLATES, '');
    }

    public function field(string $type, array $options) {
        $inputType = null;

        switch($type) {
            case 'text':
            case 'number':
            case 'date':
            case 'varchar':
                $inputType = 'input';
                break;
            case 'longtext':
                $inputType = 'textarea';
                break;
            case 'button':
            case 'submit':
                $inputType = 'button';
                break;
            case 'boolean':
                $inputType = 'boolean';
                break;
            case 'file':
                $inputType = 'file';
                break;
            case 'nodes':
                $inputType = 'title_content';
                break;
        }

        if(!$inputType) {
            // TODO: Error handling
            return false;
        }

        if(!isset($options['type'])) {
            $options['type'] = $type;
        }

        $this->fields[] = $this->view->load('form/fields/' . $inputType, $options, true);

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