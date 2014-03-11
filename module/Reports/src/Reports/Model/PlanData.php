<?php
namespace Reports\Model;

// Simple class for holding plan data to make passing to view easier
class PlanData 
{
    public $id; // Plan id
    public $metaFlag; // Has meta?
    public $descriptions; // Text of either outcome or assessment
    
    public function __construct($id, $meta)
    {
        $this->id = $id;
        $this->metaFlag = $meta;
        $this->descriptions = array();
    }
}