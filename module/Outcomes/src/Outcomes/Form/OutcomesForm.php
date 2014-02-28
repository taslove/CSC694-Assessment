<?php

namespace Outcomes\Form;

use Zend\Form\Form;

class OutcomesForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('outcomes');
        $this->setAttribute('method', 'post');
        
        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type' => 'hidden',
            ),
        ));
        
        $this->add(array(
            'name' => 'program_id',
            'attributes' => array(
                'type' => 'hidden',
            ),
        ));
        
        $this->add(array(
            'name' => 'outcome_text',
            'attributes' => array(
                'type' => 'textarea',
            ),
            'options' => array(
                'label' => 'Outcome text',
            ),
        ));
        $this->add(array(
            'name' => 'active',
            'attributes' => array(
                'type' => 'text',
            ),
            'options' => array(
                'label' => 'Active',
            ),
        ));
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Go',
                'class'  => 'btn btn-primary btn-lg',
                'id' => 'submitbutton',
            ),
        ));
    }
}