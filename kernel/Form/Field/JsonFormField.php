<?php

namespace Lampion\Form\Field;

use Lampion\Form\FormField;

class JsonFormField extends FormField {

    public function submit($data) {
        return json_encode($data);
    }

}