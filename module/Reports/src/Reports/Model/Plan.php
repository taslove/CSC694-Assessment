<?php

namespace Reports\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;

class Plan implements InputFilterAwareInterface
{
    public $id;
    public $outcome;

    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->outcome = (isset($data['outcome_text'])) ? $data['outcome_text'] : null;
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
                        ),
                    ),
                ),
                'outcome_text' => 'outcome_text',
                'required' => false,
                'validators' => array(
                    array(
                        'outcome_text' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                        ),
                    ),
                ),
                
            )));

            
            
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}