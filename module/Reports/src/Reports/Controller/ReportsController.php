<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Reports\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Reports\Model\UnitTable;


class ReportsController extends AbstractActionController
{
   protected $tableResults;

   public function indexAction()
    {
        return new ViewModel(array(
            'reports' => $this->getUnitTable()->getAllUnits(),
        ));
    }
    
    public function viewAllReportsAction()
    {
        return new ViewModel();
    }
    
    public function addReportAction()
    {
        return new ViewModel();
    }
    
    public function viewReportAction()
    {
        return new ViewModel();
    }
    
    public function getUnitTable()
    {
        if (!$this->tableResults) {
            $this->tableResults = $this->getServiceLocator()
                       ->get('UnitTable');
                    
        }
        return $this->tableResults;
    }
}
