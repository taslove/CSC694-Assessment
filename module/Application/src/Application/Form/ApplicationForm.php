<?php

namespace Application\Form;

use Zend\Form\Form;

class ApplicationForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('login');
        $this->setAttribute('method', 'get');

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Plans',
                'action' => 'plans',
                'id' => 'submitbutton',
                'class' => 'btn btn-primary btn-lg',
            ),
        ));
        
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Outcomes',
                'action' => 'outcomes',
                'id' => 'submitbutton',
                'class' => 'btn btn-primary btn-lg',
            ),
        ));
        
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Reports',
                'action' => 'reports',
                'id' => 'submitbutton',
                'class' => 'btn btn-primary btn-lg',
            ),
        ));
        
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Admin',
                'action' => 'admin',
                'id' => 'submitbutton',
                'class' => 'btn btn-primary btn-lg',
            ),
        ));
    }
}