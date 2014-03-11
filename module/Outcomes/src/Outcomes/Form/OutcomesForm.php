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
        $this->setAttribute('class', 'form-horizontal');
        $this->setAttribute('name', 'rtaform');
        
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
                'rows' => '6',
                'cols' => '90',
                'placeholder' => "",
            ),
        ));
        $this->add(array(
            'name' => 'active_flag',
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