<?php

namespace Admin\Form;

use Zend\Form\Form;

class UserForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('user');
        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'id',
            'type' => 'Zend\Form\Element\Hidden',
            'attributes' => array(
                'class' => 'form-control',
                'id' => 'id',
            ),
        ));
        $this->add(array(
            'name' => 'first_name',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'class'=> 'form-control',
                'id' => 'first_name',
            ),
            'options' => array(
                'label' => 'First Name',
            ),
        ));
        $this->add(array(
            'name' => 'middle_init',          
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'class'=> 'form-control',
                'id' => 'middle_init',
            ),
            'options' => array(
                'label' => 'Middle Initial',
            ),
        ));
        $this->add(array(
            'name' => 'email',          
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'class'=> 'form-control',
                'id' => 'email',
            ),
            'options' => array(
                'label' => 'Email',
            ),
        ));
        $this->add(array(
            'name' => 'last_name',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'class'=> 'form-control',
                'id' => 'last_name',
            ),
            'options' => array(
                'label' => 'Last Name',
            ),
        ));
         $this->add(array(
            'name' => 'user_roles',
            'type' => 'Zend\Form\Element\MultiCheckbox',
            'attributes' => array(
                'class'=> 'checkbox-inline',
                'id' => 'user_roles',
            ),
            'options' => array(
                'label' => 'User Roles',
                'value_options' => array(
                    '1' => 'Admin',
                    '2' => 'Chair',
                    '3' => 'User',
                    '4' => 'Assessor',
                    '5' => 'Committee',
                ),
            ),
        ));
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Go',
                'id' => 'submitbutton',
                'class'=> 'btn btn-default',
            ),
        ));
    }
}