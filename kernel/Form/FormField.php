<?php

namespace Lampion\Form;

use Lampion\Application\Application;
use Lampion\View\View;

/**
 * Abstract class of a form field, used for creating custom form fields
 * @author Matyáš Teplý
 */
abstract class FormField {

    public $view;

    public function __construct() {
        $this->view = new View(ROOT . APP . Application::name() . TEMPLATES . 'form/field', Application::name());
    }

    public function template(string $path, $options) {
        return $this->view->load($path, $options, true);
    }

    /**
     * All the logic happens in this method
     * @param array $data
     * @return mixed
     */
    abstract public function submit($data);

    /**
     * Returns field's template
     * @param array $options
     * @return string
     */
    abstract public function display($options);

}