<?php

namespace Lampion\Form\Field;

use Lampion\Entity\EntityManager;
use Lampion\Form\FormField;
use Lampion\View\View;

class EntityFormField extends FormField {

    public function display($options) {
        $em = new EntityManager();

        $options['entities'] = $em->all($options['metadata']->entity);

        $view = new View(KERNEL_TEMPLATES, '');

        return $view->load('form/field/entity', $options, true);
    }

    public function submit($data) {
        return $data;
    }

}