<?php
namespace Reports\Model;

class PlanData 
{
    public $id;
    public $metaFlag;
    public $descriptions;
    
    public function __construct($id, $meta)
    {
        $this->id = $id;
        $this->metaFlag = $meta;
        $this->descriptions = array();
    }
}