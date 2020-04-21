<?php

namespace Lampion\Form\Field;

use Lampion\Form\FormField;

class JsonFormField extends FormField {

    public function submit($data) {
        var_dump($this->view);

        return $data;
    }

}