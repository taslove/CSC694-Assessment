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
use Reports\Model\ReportTable;
use Reports\Model\PlanTable;



class ReportsController extends AbstractActionController
{
   protected $tableResults;

   public function indexAction()
    {
      return new ViewModel();
    }
    
    public function viewAllReportsAction()
    {
                  $planId = 370;

      return new ViewModel(array(
            'reports' => $this->getReports($planId)->getReports($planId),
            //'plans' => $this->getPlans()->getPlans(),
        ));
    }
    
    public function addReportAction()
    {
        return new ViewModel();
    }
    
    public function viewReportAction()
    {
      $planId = $this->params()->fromRoute('pid');
      $results = $this->getReports($planId)->getReports($planId);
         foreach ($results as $result){
            $reportArray[] = $result;
         }
        return new ViewModel(array(
         'report' => $reportArray,
        ));
    }
    
    public function modifyReportAction()
    {
      $planId = $this->params()->fromRoute('pid');
      $results = $this->getReports($planId)->getReports($planId);
         foreach ($results as $result){
            $reportArray[] = $result;
         }
        return new ViewModel(array(
         'report' => $reportArray,
        ));
    }
    
    public function getPlans()
    {

      if (!$this->tableResults) {
            $this->tableResults = $this->getServiceLocator()
                       ->get('PlanTable');
        }
        return $this->tableResults;
    }
    
    public function getReports(){
      if (!$this->tableResults) {
            $this->tableResults = $this->getServiceLocator()
                       ->get('ReportTable');
        }
        return $this->tableResults;
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
