<?php

namespace Selections\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Adapter;

class UnitWidget extends AbstractHelper
{
    protected $tableResults;
    private $partialFile = 'partial/unitWidget';
    
    public function __invoke()
    {
        
        // get units
        $results = $this->getSelectionsTables()->getUnits();
        // iterate over database results forming a php array
        foreach ($results as $result) : 
            $unitarray[] = $result;
        endforeach;
        
        return $this->getView()->render('partial/unitWidget', $unitarray);
    }
    
    
    public function getSelectionsTables()
    {
        if (!$this->tableResults) {
            $this->tableResults = $this->getServiceLocator()
                                       ->get('Selections\Model\SelectionsTables');
                    
        }
        return $this->tableResults;
    }
   
    public function getPartialFile()
    {
        return $this->partialFile;
    }
    
    public function setPartialFile($partialFile)
    {
        $this->partialFile = $partialFile;
    }
}
