<?php

namespace Reports\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Reports implements InputFilterAwareInterface
{
    public $id;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->uid = (isset($data['id'])) ? $data['id'] : null;
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
                'id' => 'id',
                'required' => false,
                'validators' => array(
                    array(
                        'id' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 3,
                            'max' => 3,
                        ),
                    ),
                ),
            )));

            
            
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}