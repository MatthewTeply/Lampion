<?php

namespace Lampion\Form\Field;

use Lampion\Form\FormField;

/**
 * JSON form field
 * @author Matyáš Teplý
 */
class JsonFormField extends FormField {

    public function submit($data) {
        return json_encode($data);
    }

}