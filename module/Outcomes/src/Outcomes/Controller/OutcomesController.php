<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Outcomes\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Adapter;

class OutcomesController extends AbstractActionController
{ 
    protected $tableResults;
    
    public function indexAction()
    {
        return new ViewModel(array(
            'outcomes' => $this->getModelOutcomesTable()->getAllOutcomes(),
            'units' => $this->getModelOutcomesTable()->getAllUnits(),
        ));
    }
    
    public function getModelOutcomesTable()
    {
        if (!$this->tableResults) {
            $this->tableResults = $this->getServiceLocator()
                       ->get('Outcomes\Model\OutcomesTable');
                    
        }
        return $this->tableResults;
    }
    
}
