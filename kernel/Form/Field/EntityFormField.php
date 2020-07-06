<?php

namespace Lampion\Form\Field;

use Lampion\Debug\Console;
use Lampion\Entity\EntityManager;
use Lampion\Form\FormField;
use Lampion\View\View;

/**
 * Entity form field
 * @author Matyáš Teplý
 */
class EntityFormField extends FormField {

    public function display($options) {
        $em = new EntityManager();

        $options['entities'] = false;
        $options['entity']   = false;

        if(isset($options['metadata'])) {
            $options['entities'] = $em->all($options['metadata']->entity);

            if(!empty($options['entities'])) {
                foreach($options['entities'] as $key => $entity) {
                    if(in_array($entity, (array)$options['value'])) {
                        $options['entities'][$key]->selected = true;
                    }
                }
            }
        }

        else {
            $options['entity'] = $options['value'];
        }

        $view = new View(KERNEL_TEMPLATES, '');

        return $view->load('form/field/entity', $options, true);
    }

    public function submit($data) {
        return json_encode(!is_array($data) ? [$data] : $data);
    }

}