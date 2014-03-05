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
use Reports\Model\PlanData;
use Reports\Forms\ReportForm;
use Zend\View\Model\JsonModel;

class ReportsController extends AbstractActionController
{
   // This holds table results for certain methods
   protected $tableResults;
   
    // get these values from the session namespace
   protected $userRole = 3;
   protected $userID = 9;   

   // Returns main index with left select options and blank right side
   public function indexAction()
    {
         $sl = $this->getServiceLocator();
         $form = $sl->get('FormElementManager')->get('Reports\forms\SelectForm');

         // if general user - only view
        // get all units, since only view option is displayed
        if ($this->userRole == null){
            $results = $this->getGenericQueries()->getUnits();
            // iterate over database results forming a php array
            foreach ($results as $result){
                $unitarray[] = $result;
            }
            return new ViewModel(array(
                'useractions' => array('View'),
                'units' => $unitarray,
            ));
        }
        else{  // user in table with role - show actions
               // wait to populate units until action chosen
            return new ViewModel(array(
                'useractions' => array('View', 'Add', 'Modify'),
                'form' => $form,
                
            ));
        }
      return new ViewModel();
    }
    
    // Called to get plan data after selection is made on left menu of view
    public function viewPlansAction()
    {
      
      // Get post data
      $jsonData = $this->getRequest()->getContent();

      // Get the plans
      $results = $this->getReports($jsonData)->getPlans($jsonData);
      if(count($results) > 0){

         // Create array to hold the plans
         $plans = array();
         $sl = $this->getServiceLocator();
   
         // Start with an empty plan
         $currPlan = new PlanData(null, null);
         $needsAdding = true;
         
         // Loop through results, adding all outcomes for same plan id
         // to same planData, otherwise start a new planData
         // This was created here when trying a different approach,
         // May remove planData class as it doesn't serve much purpose anymore
         foreach ($results as $result){
            $test[] = $result;
            $needsAdding = true;
            if(is_null($currPlan->id)){
               $currPlan = new PlanData($result['id'], $result['meta_flag']);
               array_push($currPlan->descriptions, $result['text']);
            }elseif($currPlan->id == $result['id']){
               array_push($currPlan->descriptions, $result['text']);
               
            }else{
               array_push($plans, $currPlan);
               $currPlan = new PlanData($result['id'], $result['meta_flag']);
               array_push($currPlan->descriptions, $result['text']);
            }
         }
         
         // Add last plan
         array_push($plans, $currPlan);
         
         $data = json_decode($jsonData, true);
         $action = $data['action'];
   
            
         $partialView = new ViewModel(array(
            'plans' => $plans, 'action' => $action, 'results' => true,
         ));
         
         $partialView->setTerminal(true);
         return $partialView;
      }else{
         $partialView = new ViewModel(array(
            'results' => false,
         ));
      }
      
      
      $partialView->setTerminal(true);
      return $partialView;
    }
    
    // Display add report view
    public function addReportAction()
    {
      
      $planId = $this->params()->fromPost('id');
      
      // Check if report already exists
      $count = $this->getReports()->reportExists($planId);
      if($count > 0){
         $partialView = new ViewModel(array(
            'results' => true,
         ));
         $partialView->setTerminal(true);
         return $partialView;
      }
   
      $results = $this->getReports()->getPlanForAdd($planId);
      $sl = $this->getServiceLocator();
      $form = $sl->get('FormElementManager')->get('Reports\forms\ReportForm');
                  
                  $descriptions = array();

            foreach ($results as $result){
                array_push($descriptions, $result['text']);

               $planData[] = $result;
            }
         
         $partialView = new ViewModel(array(
            'results' => false, 'form' => $form, 'planData' => $planData, 'descriptions' => $descriptions,
         ));
         
         $partialView->setTerminal(true);

         return $partialView;
    }

    
    // Gets individual report details when user selects a plan they wish
    // to view the report for
    public function viewReportAction()
    {
      
      $planId = $this->params()->fromPost('id');
      $results = $this->getReports()->getReport($planId);
      
      if(count($results) > 0){
         $descriptions = array();
         foreach ($results as $result){
            array_push($descriptions, $result['text']);
            $reportArray[] = $result;
         }
      
         $partialView = new ViewModel(array(
            'report' => $reportArray, 'descriptions' => $descriptions, 'results' => true,
         ));
      }else{
         $partialView = new ViewModel(array(
            'results' => false,
         ));
      }
      
      
      $partialView->setTerminal(true);
      return $partialView;
    }
    
    // Called after user selects a plan they with to modify the report for
    // Displays report data associated with user selected plan 
    public function modifyReportAction()
    {
      
      $planId = $this->params()->fromPost('id');
      $results = $this->getReports()->getReport($planId);
      
      if(count($results) > 0){
          $descriptions = array();
            foreach ($results as $result){
               array_push($descriptions, $result['text']);
               $reportArray[] = $result;
            }
         $sl = $this->getServiceLocator();
         $form = $sl->get('FormElementManager')->get('Reports\forms\ReportForm');
         
         $partialView = new ViewModel(array(
            'report' => $reportArray, 'descriptions' => $descriptions, 'form' => $form, 'results' => true,
         ));
         
      }else{
         $partialView = new ViewModel(array(
            'results' => false,
         ));
         
      }
      
         $partialView->setTerminal(true);

         return $partialView;
    }
    
    
    // This is called after user makes modifications to the report data and
    // wants to send it to the database
    public function updateReportAction()
    {
      $this->getServiceLocator()->get('ReportTable')
           ->updateReport($this->params()->fromPost('id'),
                          $this->params()->fromPost('population'),
                          $this->params()->fromPost('results'),
                          $this->params()->fromPost('conclusions'),
                          $this->params()->fromPost('actions'));

      $this->redirect()->toRoute('index');
    }
    
    public function addNewReportAction()
    {
      $this->getServiceLocator()->get('ReportTable')
           ->addReport($this->params()->fromPost('id'),
                          $this->params()->fromPost('population'),
                          $this->params()->fromPost('results'),
                          $this->params()->fromPost('conclusions'),
                          $this->params()->fromPost('actions'));

      $this->redirect()->toRoute('index');
    }
    
    // Used to call methods in the ReportTable class
    public function getReports(){
      if (!$this->tableResults) {
            $this->tableResults = $this->getServiceLocator()
                       ->get('ReportTable');
        }
        return $this->tableResults;
    }
    
    public function getGenericQueries()
    {
        if (!$this->tableResults) {
            $this->tableResults = $this->getServiceLocator()
                                       ->get('Application\Model\AllTables');
                    
        }
        return $this->tableResults;
    }
    
    // Creates list of available units (departments/programs)
    // based on user role and privileges.
    public function getUnitsAction()
    {
        // get action from id in url
        $actionChosen = $this->params()->fromRoute('id', 0);
     
        // get units for that action
        if ($actionChosen == 'View'){
            $results = $this->getGenericQueries()->getUnits();
        }
        else{
            $results = $this->getGenericQueries()->getUnitsByPrivId($this->userID);
        }
      
        // iterate through results forming a php array
        foreach ($results as $result){
            $unitData[] = $result;
        }
      
        // encode results as json object
        $jsonData = new JsonModel($unitData);
        
        return $jsonData;
    }
    public function getProgramsAction()
    {
        // get unit from id in url
        $unitChosen = $this->params()->fromRoute('id', 0);
        // get programs for that unit
        $results = $this->getGenericQueries()->getProgramsByUnitId($unitChosen);
      
        // iterate through results forming a php array
        foreach ($results as $result){
            $programData[] = $result;
        }
        // encode results as json object
        $jsonData = new JsonModel($programData);
        return $jsonData;
    }
    
    public function getYearsAction()
    {
        // Get post data
      $jsonData = $this->getRequest()->getContent();

      // Get the plans
      $results = $this->getReports($jsonData)->getYears($jsonData);
      
      // iterate through results forming a php array
        foreach ($results as $result){
            $yearData[] = $result;
        }
      
        // encode results as json object
        $jsonData = new JsonModel($yearData);
        
        return $jsonData;
    }
}
