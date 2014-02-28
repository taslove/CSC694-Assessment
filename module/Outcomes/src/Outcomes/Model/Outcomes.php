<?php

namespace Outcomes\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Outcomes implements InputFilterAwareInterface
{
    // database attributes
    public $oid;
    public $programId;
    public $outcomeText;
    public $activeFlag;
    
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->oid = (isset($data['id'])) ? $data['id'] : null;
        $this->programId = (isset($data['program_id'])) ? $data['program_id'] : null;
        $this->outcomeText = (isset($data['outcome_text'])) ? $data['outcome_text'] : null;
        $this->activeFlag = (isset($data['active_flag'])) ? $data['active_flag'] : null;
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
                'filters' => array(
                    array('name' => 'Int'),
                ),
            )));
            
                $inputFilter->add($factory->createInput(array(
                'name' => 'program_id',
                'required' => true,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            )));
            
            $inputFilter->add($factory->createInput(array(
                'name' => 'outcome_text',
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 1000,
                        ),
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'active_flag',
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