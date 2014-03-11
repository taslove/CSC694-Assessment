<?php

namespace Admin\Form;

use Zend\Form\Form;

class UnitForm extends Form
{
   protected $assessors;
   protected $liaisons;
    
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('program');

    }
    
    function buildForm()
    {
        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'id',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Unit ID',
            ),
            'attributes' => array(
                'class' => 'form-control',
                'id' => 'id',
            ),
        ));
        $this->add(array(
            'name' => 'type',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'class'=> 'form-control',
                'id' => 'type',
            ),
            'options' => array(
                'label' => 'Type',
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
            'name' => 'assessor_1',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'class'=> 'form-control',
                'id' => 'assessor_1',
            ),
            'options' => array(
                'label' => 'Assessor #1',
                'empty_option' => '- Select User -',
                'value_options' => $this->getAssessors(),
            ),
        ));
        $this->add(array(
            'name' => 'assessor_2',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'class'=> 'form-control',
                'id' => 'assessor_2',
            ),
            'options' => array(
                'label' => 'Assessor #2',
                'empty_option' => '- Select User -',
                'value_options' => $this->getAssessors(),
            ),
        ));
        
        $this->add(array(
            'name' => 'liaison_1',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'class'=> 'form-control',
                'id' => 'liaison_1',
            ),
            'options' => array(
                'label' => 'Liaison #1',
                'empty_option' => '- Select User -',
                'value_options' => $this->getLiaisons(),
            ),
        ));
        $this->add(array(
            'name' => 'liaison_2',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'class'=> 'form-control',
                'id' => 'liaison_2',
            ),
            'options' => array(
                'label' => 'Liaison #2',
                'empty_option' => '- Select User -',
                'value_options' => $this->getLiaisons(),
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
    public function setAssessors($assessors)
    {
        $this->assessors = $assessors;
        return $assessors;
    }
    
    public function getAssessors()
    {
        return $this->assessors;
    }
      
    public function setLiaisons($liaisons)
    {
        $this->liaisons = $liaisons;
        return $liaisons;
    }
    
    public function getLiaisons()
    {
        return $this->liaisons;
    }
}