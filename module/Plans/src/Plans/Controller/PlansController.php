<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Plans\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class PlansController extends AbstractActionController
{

   protected $tableResults;

// Sample dump logic used for debugging, use as needed   
//      foreach ($this->getDatabaseData()->getAllYears() as $data) :
//          var_dump($data);
//      endforeach;
//      exit();

//      var_dump($request);
//      exit();

   public function getDatabaseData()
   {
       if (!$this->tableResults) {
           $this->tableResults = $this->getServiceLocator()
                      ->get('Plans\Model\DatabaseSql');
       }
       return $this->tableResults;
   }
   
   public function indexAction()
   {
        $request = $this->getRequest();
        if ($request->isPost()) {
         
            $action = $request->getPost('textAction');
            $department = $request->getPost('textDepartment');
            $program = $request->getPost('textProgram');
            $year = $request->getPost('textYear');
            $form = $request->getPost('formGetPlan');                 
                 
            if ($form == "formGetPlan" && $action == "Add") {
               // Go to the add plan page
                  return $this->redirect()->toRoute('plans', array('action'=>'addplan',
                                                    'type' => $action,
                  			            'department' => $department,                                                                
						    'program' => $program,
						    'year' => $year
                                                   ));
            }
            else {
               // Go to the modify plan page
               return new ViewModel(array(
                  'returnAction' => $action,
                  'returnDepartment' => $department,
                  'returnProgram' => $program,
                  'returnYear' => $year,
                  'outcomes' => $this->getDatabaseData()->getOutcomes($department, $program, $year),
                  'plans' => $this->getDatabaseData()->getPlans($department, $program, $year),                  
               ));               
            }
         }
         else {
            // Initial Page Load, get request
            return new ViewModel(array(
               'returnAction' => null,
               'returnDepartment' => null,
               'returnProgram' => null,
               'returnYear' => null,
            ));
         }
   }


   public function viewOnlyPlanAction()
   {
      // pull data from the route url
      $action = $this->params()->fromRoute('type', 0);
      $planId = (int) $this->params()->fromRoute('planId', 0);
      $department = $this->params()->fromRoute('department', '');
      $program = $this->params()->fromRoute('program', '');
      $year = (int) $this->params()->fromRoute('year', 0);
               
      $request = $this->getRequest();
      if ($request->isPost()) {
         /* perfomr post request action here */   
      }
      else {
         // Initial Page Load, get request
         return new ViewModel(array(
            'planId' => $planId,
            'returnAction' => $action,
            'returnDepartment' => $department,
            'returnProgram' => $program,
            'returnYear' => $year,
            'outcomes' => $this->getDatabaseData()->getOutcomesByPlanId($planId),
            'plan' => $this->getDatabaseData()->getPlanByPlanId($planId),
         ));
      }
   }
   
   
   public function modifyPlanAction()
   {
      // pull data from the route url
      $action = $this->params()->fromRoute('type', 0);
      $planId = (int) $this->params()->fromRoute('planId', 0);
      $department = $this->params()->fromRoute('department', '');
      $program = $this->params()->fromRoute('program', '');
      $year = (int) $this->params()->fromRoute('year', 0);
      
      $request = $this->getRequest();      
      
      if ($request->isPost()) {
            $button = $request->getPost('formSaveChanges');
            
            if ($button == 'formSaveChanges') {
         
               $planId = $request->getPost('planId');
               $assessmentMethod = $request->getPost('textAssessmentMethod');
               $population = $request->getPost('textPopulation');
               $sampleSize = $request->getPost('textSamplesize');
               $assessmentDate = $request->getPost('textAssessmentDate');
               $cost = $request->getPost('textCost');
               $analysisType = $request->getPost('textAnalysisType');
               $administrator = $request->getPost('textAdministrator');
               $analysisMethod = $request->getPost('textAnalysisMethod');
               $scope = $request->getPost('textScope');
               $feedback = $request->getPost('textFeedback');
               $feedbackFlag = $request->getPost('textFeedbackFlag');
               $planStatus = $request->getPost('textPlanStatus');

               $this->getDatabaseData()->updatePlan($planId,$assessmentMethod,$population,$sampleSize,$assessmentDate,$cost,$analysisType,$administrator,$analysisMethod,$scope,$feedback,$feedbackFlag,$planStatus);
               return $this->redirect()->toRoute('plans');
            }
            else {
               return $this->redirect()->toRoute('plans');
            }
      }
      else {
         // Initial Page Load, get request
         return new ViewModel(array(
            'planId' => $planId,
            'returnAction' => $action,
            'returnDepartment' => $department,
            'returnProgram' => $program,
            'returnYear' => $year,
            'outcomes' => $this->getDatabaseData()->getOutcomesByPlanId($planId),
            'plan' => $this->getDatabaseData()->getPlanByPlanId($planId),
         ));
      }
   }
   
   public function addplanAction()
   {
      $action = $this->params()->fromRoute('type', '');
      $department = $this->params()->fromRoute('department', '');
      $program = $this->params()->fromRoute('program', '');
      $year = (int) $this->params()->fromRoute('year', 0);
      
      $request = $this->getRequest();
      
      if ($request->isPost()) {
         
         $button = $request->getPost('formSavePlan');
                                         
         //check form submission, Enter or Start New Plan
         if ($button == "formSavePlan") {

            // Submit the plan
            $outcomeId = $request->getPost('radioOutcomes');
            $year = $request->getPost('year');
            $assessmentMethod = $request->getPost('textAssessmentMethod');
            $population = $request->getPost('textPopulation');
            $sampleSize = $request->getPost('textSamplesize');
            $assessmentDate = $request->getPost('textAssessmentDate');
            $cost = $request->getPost('textCost');
            $analysisType = $request->getPost('textAnalysisType');
            $administrator = $request->getPost('textAdministrator');
            $analysisMethod = $request->getPost('textAnalysisMethod');
            $scope = $request->getPost('textScope');
            $feedback = $request->getPost('textFeedback');
            $feedbackFlag = $request->getPost('textFeedbackFlag');
            $planStatus = $request->getPost('textPlanStatus');
                              
            // insert into plan table and obtain the primary key of the insert
            $planId = $this->getDatabaseData()->insertPlan($year, $assessmentMethod,$population,$sampleSize,$assessmentDate,$cost,$analysisType,$administrator,$analysisMethod,$scope,$feedback,$feedbackFlag,$planStatus);                  
           
            // insert into the outcome table
            $this->getDatabaseData()->insertPlanOutcome($outcomeId, $planId['maxId']);
                  
            return $this->redirect()->toRoute('plans');
         }
      }
      else {
         // Initial Page Load, get request
         return new ViewModel(array(
            'returnAction' => $action,
            'returnDepartment' => $department,
            'returnProgram' => $program,
            'returnYear' => $year,
            'outcomes' => $this->getDatabaseData()->getUniqueOutcomes($department, $program, $year),
         ));
      }
   }
}
