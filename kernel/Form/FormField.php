<?php

namespace Lampion\Form;

use Lampion\Application\Application;
use Lampion\View\View;

abstract class FormField {

    public $view;

    public function __construct() {
        $this->view = new View(ROOT . APP . Application::name() . TEMPLATES . 'form/field', Application::name());

        $this->view->setFilter('toArray', function ($stdClassObject) {
            $response = array();
            foreach ($stdClassObject as $key => $value) {
                $response[] = array('key' => $key, 'value' => $value);
            }
            return $response;
        });
    }

    public function template(string $path, $options) {
        return $this->view->load($path, $options, true);
    }

    /**
     * All the logic happens in this method
     * @return mixed
     */
    abstract public function submit($data);

}