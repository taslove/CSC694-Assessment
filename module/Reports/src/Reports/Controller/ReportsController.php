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
   protected $tableResults;
   
    // get these values from the session namespace
    protected $userRole = 3;
    protected $userID = 9;   

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
      // Get route vars
      $programId = $this->params()->fromRoute('pid');
      $year = $this->params()->fromRoute('year');
      
      // Get the plans
      $results = $this->getReports($programId, $year)->getPlans($programId, $year);
      
      // Create array to hold the plans
      $plans = array();
      $sl = $this->getServiceLocator();

      // Start with an empty plan
      $currPlan = new PlanData(null);
      $needsAdding = true;
      
      // Loop through results, adding all outcomes for same plan id
      // to same planData, otherwise start a new planData
      // This was created here when trying a different approach,
      // May remove planData class as it doesn't serve much purpose anymore
      foreach ($results as $result){
         if(is_null($currPlan->id)){
            $currPlan = new PlanData($result['plan_id']);
            // Add form
            $currPlan->form = $sl->get('FormElementManager')->get('Reports\forms\PlanForm');
            array_push($currPlan->outcomes, $result['outcome_text']);
         }elseif($currPlan->id == $result['plan_id']){
            array_push($currPlan->outcomes, $result['outcome_text']);
            
         }else{
            $currPlan = new PlanData($result['plan_id']);
            // Add form
            $currPlan->form = $sl->get('FormElementManager')->get('Reports\forms\PlanForm');
            array_push($currPlan->outcomes, $result['outcome_text']);
            array_push($plans, $currPlan);
            $needsAdding = false;
         }
      }
      
      // Add last plan if needed
      if($needsAdding){
         array_push($plans, $currPlan);
      }

     return new ViewModel(array(
      'plans' => $plans,
     ));
    }
    
    // Display add report view
    public function addReportAction()
    {
      
      $sl = $this->getServiceLocator();
      $form = $sl->get('FormElementManager')->get('Reports\forms\ReportForm');
      $planId = $this->params()->fromPost('id');
      $results = $this->getReports($planId)->getReports($planId);
      $outcomes = array();
         foreach ($results as $result){
            array_push($outcomes, $result['outcome_text']);
            $reportArray[] = $result;
         }
        return new ViewModel(array(
         'report' => $reportArray, 'outcomes' => $outcomes, 'form' => $form
        ));
    }
    
    // Called from addReport view to insert new report into DB
    public function insertReportAction()
    {
      $this->getServiceLocator()->get('ReportTable')
           ->addReport($this->params()->fromPost('id'),
                          $this->params()->fromPost('population'),
                          $this->params()->fromPost('results'),
                          $this->params()->fromPost('conclusions'),
                          $this->params()->fromPost('actions'));

      return new ViewModel(array(
      ));
    }
    
    // Gets individual report details when user selects a plan they wish
    // to view the report for
    public function viewReportAction()
    {
      
      $planId = $this->params()->fromPost('id');
      $results = $this->getReports()->getReports($planId);
      $outcomes = array();
         foreach ($results as $result){
            array_push($outcomes, $result['outcome_text']);
            $reportArray[] = $result;
         }
        return new ViewModel(array(
         'report' => $reportArray, 'outcomes' => $outcomes,
        ));
    }
    
    // Called after user selects a plan they with to modify the report for
    // Displays report data associated with user selected plan 
    public function modifyReportAction()
    {
      $sl = $this->getServiceLocator();
      $form = $sl->get('FormElementManager')->get('Reports\forms\ReportForm');
      $planId = $this->params()->fromPost('id');
      $results = $this->getReports($planId)->getReports($planId);
      $outcomes = array();
         foreach ($results as $result){
            array_push($outcomes, $result['outcome_text']);
            $reportArray[] = $result;
         }
        return new ViewModel(array(
         'report' => $reportArray, 'outcomes' => $outcomes, 'form' => $form
        ));
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

      return $view;
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
}
