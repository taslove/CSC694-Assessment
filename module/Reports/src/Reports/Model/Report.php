<?php

namespace Reports\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;

class Report implements InputFilterAwareInterface
{
    public $id;
    public $planid;
    public $ts;
    public $population;
    public $results;
    public $conclusions;
    public $actions;
    public $feedback;
    public $feedback_flag;
    public $report_status;

    protected $inputFilter;

    public function exchangeArray($data)
    {
        
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->planid = (isset($data['planid'])) ? $data['planid'] : null;
        $this->ts = (isset($data['ts'])) ? $data['ts'] : null;
        $this->population = (isset($data['population'])) ? $data['population'] : null;
        $this->results = (isset($data['results'])) ? $data['results'] : null;
        $this->conclusions = (isset($data['conclusions'])) ? $data['conclusions'] : null;
        $this->actions = (isset($data['actions'])) ? $data['actions'] : null;
        $this->feedback = (isset($data['feedback'])) ? $data['feedback'] : null;
        $this->feedback_flag = (isset($data['feedback_flag'])) ? $data['feedback_flag'] : null;
        $this->report_status = (isset($data['report_status'])) ? $data['report_status'] : null;
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
                'required' => true,
                'validators' => array(
                    array(
                        'id' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                        ),
                    ),
                ),
                'planid' => 'planid',
                'required' => false,
                'validators' => array(
                    array(
                        'planid' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 0,
                            'max' => 100,
                        ),
                    ),
                ),
                'ts' => 'ts',
                'required' => false,
                'validators' => array(
                    array(
                        'ts' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 0,
                            'max' => 100,
                        ),
                    ),
                ),
                'population' => 'population',
                'required' => false,
                'validators' => array(
                    array(
                        'population' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 0,
                            'max' => 100,
                        ),
                    ),
                ),
                'results' => 'results',
                'required' => false,
                'validators' => array(
                    array(
                        'results' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 0,
                            'max' => 100,
                        ),
                    ),
                ),
                'conclusions' => 'conclusions',
                'required' => false,
                'validators' => array(
                    array(
                        'conclusions' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 0,
                            'max' => 100,
                        ),
                    ),
                ),
                'actions' => 'actions',
                'required' => false,
                'validators' => array(
                    array(
                        'actions' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 0,
                            'max' => 100,
                        ),
                    ),
                ),
                'feedback' => 'feedback',
                'required' => false,
                'validators' => array(
                    array(
                        'feedback' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 0,
                            'max' => 100,
                        ),
                    ),
                ),
                'feedback_flag' => 'feedback_flag',
                'required' => false,
                'validators' => array(
                    array(
                        'feedback_flag' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 0,
                            'max' => 100,
                        ),
                    ),
                ),
                'report_status' => 'report_status',
                'required' => false,
                'validators' => array(
                    array(
                        'report_status' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 0,
                            'max' => 100,
                        ),
                    ),
                ),
            )));

            
            
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}