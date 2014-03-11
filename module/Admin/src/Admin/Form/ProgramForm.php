<?php
/*
 *  Program Form
 */
namespace Admin\Form;

use Zend\Form\Form;

class ProgramForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('program');
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
            'name' => 'name',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'class'=> 'form-control',
                'id' => 'name',
            ),
            'options' => array(
                'label' => 'Name',
            ),
        ));

        $this->add(array(
            'name' => 'active_flag',
            'type' => 'Zend\Form\Element\Checkbox',
            'attributes' => array(
                'class'=> 'form-control',
                'id' => 'active_flag',
            ),
            'options' => array(
                'label' => 'Active',
            ),
        ));
         $this->add(array(
            'name' => 'unit_id',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'class'=> 'form-control',
                'id' => 'unit_id',
            ),
            'options' => array(
                'label' => 'Unit ID',
                'value_options' => array(
                    'ACC'=>'ACC',
                    'BIO'=>'BIO',
                    'CSC'=>'CSC',
                    'HST'=>'HST',
                    'MTH'=>'MTH',
                    'PHL'=>'PHL'
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