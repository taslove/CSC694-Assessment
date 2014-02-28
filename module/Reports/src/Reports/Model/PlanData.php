<?php
namespace Reports\Model;

class PlanData 
{
    public $id;
    public $form;
    
    public function __construct($id)
    {
        $this->id = $id;
        $this->outcomes = array();
    }
    
    
}