<?php

namespace Lampion\Form;

use Lampion\View\View;
use Lampion\Session\Lampion as Session;

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
        $types = [
            'text'     => 'input',
            'number'   => 'input',
            'textarea' => 'textarea',
            'button'   => 'button',
            'nodes'    => 'title_content',
            'boolean'  => 'boolean',
            'date'     => 'input',
            'file'     => 'file'
        ];

        if(!isset($types[$type])) {
            // TODO: Error handling
            return false;
        }

        if(!isset($options['type'])) {
            $options['type'] = $type;
        }

        $this->fields[] = $this->view->load('form/fields/' . $types[$type], $options, true);

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