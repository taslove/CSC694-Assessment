<?php

namespace Admin\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Unit implements InputFilterAwareInterface
{
    protected $inputFilter;

    public function exchangeArray($data)
    {
        foreach($data as $id => $value){
            $this->$id = ($value)? $value: null;
        }
    }

     // Add the following method:
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
      if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory = new InputFactory();

            $inputFilter->add($factory->createInput(array(
                'name' => 'id',
                'required' => true,
            )));
            $inputFilter->add($factory->createInput(array(
                'name' => 'active_flag',
                'required' => false,
            )));
               $inputFilter->add($factory->createInput(array(
                'name' => 'assessor_1',
                'required' => false,
            )));
              $inputFilter->add($factory->createInput(array(
                'name' => 'assessor_1',
                'required' => false,
            )));
               $inputFilter->add($factory->createInput(array(
                'name' => 'liaison_1',
                'required' => false,
            )));
                $inputFilter->add($factory->createInput(array(
                'name' => 'liaison_2',
                'required' => false,
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'type',
                'required' => true,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            )));
            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
}