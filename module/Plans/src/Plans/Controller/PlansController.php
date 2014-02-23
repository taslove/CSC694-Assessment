<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Plans\Controller;

use Plans\Form\CollectionUpload;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Adapter;
use Zend\View\Model\JsonModel;
use Zend\Session\Container;

use Plans\Form;
use Plans\InputFilter;
use Zend\Debug\Debug;

class PlansController extends AbstractActionController
{

   protected $tableResults;
   protected $tableResultsPlans;
   
   // get these values from the session namespace
//   protected $userRole = null;
   protected $userRole = 3;
   protected $userID = 19;   //9 = ACC, 19 = CSC

   /**
    * @var Container
    */
   protected $sessionContainer;

    
   public function __construct()
   {
      $this->sessionContainer = new Container('fileUpload');
   }

// Sample dump logic used for debugging, use as needed   
//      foreach ($this->getDatabaseData()->getAllYears() as $data) :
//          var_dump($data);
//      endforeach;
//      exit();

   /**
    * Used to access the plans specific SQL statements
    */
   public function getDatabaseData()
   {
       if (!$this->tableResultsPlans) {
           $this->tableResultsPlans = $this->getServiceLocator()
                                       ->get('Plans\Model\DatabaseSql');
       }
       return $this->tableResultsPlans;
   }

   /**
    * Used to access the generic SQL statements
    */  
   public function getGenericQueries()
   {
      if (!$this->tableResults) {
           $this->tableResults = $this->getServiceLocator()
                                      ->get('Application\Model\AllTables');
        }
        return $this->tableResults;
   }
      
   /**
    * This is the controller that gets called upon loading the plans page
    *
    * Processes
    * 1) Post Request
    * 2) Get Request
    */
   public function indexAction()
   {
      
      // get and check the request type
      $request = $this->getRequest();
      if ($request->isPost()) {
         // process post request
 
         // get the data from the form        
         $action = $request->getPost('action-menu');
         $unit = $request->getPost('unit-menu');
         $programs = $request->getPost('prog-menu');
         $year = $request->getPost('year-menu');
            
         // create session variable used to populate the titles on the next page
	 $planSession = new Container('planSession');
	 $planSession->action = $action;
         $planSession->unit = $unit;
         $planSession->programs = $programs;
         $planSession->year = $year;
            
         // determine where to go next
         if ($action == "View" || $action == "Modify") {
            return $this->redirect()->toRoute('plans', array('action'=>'listplans'));                
         }
         else {
            return $this->redirect()->toRoute('plans', array('action'=>'addplan'));
         }
      }
      else {
         // process get request
            
         // create session variable used to populate the titles on the next page
	 $planSession = new Container('planSession');
         
         // if general user - only view
         // get all units, since only view option is displayed
         if ($this->userRole == null){
            $unitarray[] = $this->getInitialUnits();
                       
            // set the correct user actions and create a session variable       
            $useractions = array('View');
            $planSession->useractions = $useractions;
                   
            return new ViewModel(array(
               'useractions' => array('View'),
               'units' => $unitarray,
            ));
         }
         else{
            
            // set the correct user actions and create a session variable       
            $useractions = array('View', 'Add', 'Modify');
            $planSession->useractions = $useractions;
            
            // user in table with role - show actions
            // wait to populate units until action chosen
            return new ViewModel(array(
               'useractions' => array('View', 'Add', 'Modify'),
            ));
         }
      }
   }
   
   /**
    * Creates list of available units (departments/programs)
    * based on user role and privileges.
    */
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
   
   /**
    * Creates a list of available programs base on the
    * user supplied department
    */
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
    
   /**
    * Creates a list of all the available years present
    * on the plans table
    */
   public function getYearsAction()
   {
      // get unit from id in url
      $yearsChosen = $this->params()->fromRoute('id', 0);

      // get all the years
      $results = $this->getGenericQueries()->getYears();
      
      // iterate through results forming a php array
      foreach ($results as $result){
         $yeararray[] = $result;
      }
  
      // encode results as json object
      $jsonData = new JsonModel($yeararray);
      return $jsonData;
   }

   /**
    * Controls listing of the plans
    * both the user actions of view and modify will come here
    *
    * Processes
    *  1) Get request
    */
   public function listPlansAction()
   {
      $request = $this->getRequest();
      
      // currenlty there is not post request      
      if ($request->isPost()) {

      }
      else {
         // process the get request
         
         // get session data
       	 $planSession = new Container('planSession');
	 $action = $planSession->action; 
         $unit = $planSession->unit;
         $programs = $planSession->programs;
         $year = $planSession->year;
         $useractions = $planSession->useractions;
                
         // Initial Page Load, get request
         // get units
         $unitarray[] = $this->getInitialUnits();
           
         // get years
         $yeararray[] = $this->getInitialYears();    
                  
         // pass array to view
         return new ViewModel(array(
            'units' => $unitarray,
            'years' => $yeararray,
                              
            'action' => $action,
            'unit' => $unit,
            'programs' => $programs,
            'year' => $year,
            'useractions' => $useractions,
            
            // get outcome and plans data
            'outcomes' => $this->getGenericQueries()->getOutcomes($unit, $programs, $year),
            'plans' => $this->getGenericQueries()->getPlans($unit, $programs, $year),   
            ));
         }
   }

   /**
    * Controller for the view plans page
    * 
    * Processes
    * 1) Get request
    */
   public function viewOnlyPlanAction()
   {
      // pull data from the route url
      $planId = (int) $this->params()->fromRoute('id', 0);                      
            
      $request = $this->getRequest();
      
      // no post requestd
      if ($request->isPost()) {
         // process post request
      }
      else {
         // process get request
         
         // get session data
       	 $planSession = new Container('planSession');
	 $action = $planSession->action; 
         $unit = $planSession->unit;
         $programs = $planSession->programs;
         $year = $planSession->year;
         $useractions = $planSession->useractions;
         
         // Initial Page Load, get request
         // get units
         $unitarray[] = $this->getInitialUnits();

         // get years
         $yearsData[] = getInitialYears();

         // Initial Page Load, get request
         return new ViewModel(array(
            'units' => $unitarray,
            'years' => $yeararray,
               
            'planId' => $planId,
            'action' => $action,
            'unit' => $unit,
            'programs' => $programs,
            'year' => $year,
            'useractions' => $useractions,
            
            'outcomes' => $this->getGenericQueries()->getOutcomesByPlanId($planId),
            'plan' => $this->getGenericQueries()->getPlanByPlanId($planId),
         ));
      }
   }
   
   /**
    * Controller used for the modify plans page
    *
    * Processess
    *    1) Post Request
    *    2) Get Request
    */
   public function modifyPlanAction()
   {
      // pull data from the route url
      $planId = (int) $this->params()->fromRoute('id', 0);
      
      $request = $this->getRequest();            
      if ($request->isPost()) {
         // process the post request
          
         // identify which button was presses 
         $button = $request->getPost('formSubmit');             
      
         // process the submit button and the save draft button
         if ($button == "formSavePlan" || $button == "formSaveDraft") {

            // set the draft flag
            $draftFlag = $this->getDraftFlag($button);
         
            // get all the data from the form
            $planId = trim($request->getPost('planId'));
            $assessmentMethod = trim($request->getPost('textAssessmentMethod'));
            $population = trim($request->getPost('textPopulation'));
            $sampleSize = trim($request->getPost('textSamplesize'));
            $assessmentDate = trim($request->getPost('textAssessmentDate'));
            $cost = trim($request->getPost('textCost'));
            $analysisType = trim($request->getPost('textAnalysisType'));
            $administrator = trim($request->getPost('textAdministrator'));
            $analysisMethod = trim($request->getPost('textAnalysisMethod'));
            $scope = trim($request->getPost('textScope'));
            $feedback = trim($request->getPost('textFeedback'));
            $feedbackFlag = trim($request->getPost('textFeedbackFlag'));
            $planStatus = trim($request->getPost('textPlanStatus'));

            //update the database
            $this->getDatabaseData()->updatePlan($planId,0,"",$assessmentMethod,$population,$sampleSize,$assessmentDate,$cost,$analysisType,$administrator,$analysisMethod,$scope,$feedback,$feedbackFlag,$planStatus,$draftFlag,$this->userID);
            return $this->redirect()->toRoute('plans');
         }
         else {
            return $this->redirect()->toRoute('plans');
         }
      }
      else {
         // process the get request
               
         // get session data
       	 $planSession = new Container('planSession');
	 $action = $planSession->action; 
         $unit = $planSession->unit;
         $programs = $planSession->programs;
         $year = $planSession->year;               
         $useractions = $planSession->useractions;
         
         // Initial Page Load, get request
         // get units
         $unitarray[] = $this->getInitialUnits();

         // get years
	 $yeararray[] = $this->getInitialYears();        
            
         // Initial Page Load, get request
         return new ViewModel(array(
            'units' => $unitarray,
            'years' => $yeararray,
               
            'planId' => $planId,
            'action' => $action,
            'unit' => $unit,
            'programs' => $programs,
            'year' => $year,
            'useractions' => $useractions,
            
            'outcomes' => $this->getGenericQueries()->getOutcomesByPlanId($planId),
            'plan' => $this->getGenericQueries()->getPlanByPlanId($planId),
         ));
      }
   }
   
   /**
    * Controller used for the add page
    *
    * Processes
    *    1) Post Request
    *    2) Get Request
    */
   public function addplanAction()
   {
      // form for uploading documents 
      $form = new CollectionUpload('file-form');
       
      $request = $this->getRequest();
      if ($request->isPost()) {
         //process the post request
         
         // get session data
       	 $planSession = new Container('planSession');	 
         $year = $planSession->year;
         $useractions = $planSession->useractions;
         
         // get button form data       
         $button = $request->getPost('formSubmit');
         $outcomeCount = $request->getPost('outcomeCount');

         // load the checked outcome box values into an array
         for ($x = 1; $x <= $outcomeCount; $x++)
         {
            $checkboxName = "checkboxOutcomes" . $x;
            $checkboxValue = $request->getPost($checkboxName);
            
            if ($checkboxValue != null) {
               $outcomeIds[] = $checkboxValue;
            }
         }
         
         // if the meta flag option was pressed go directly to the meta add page
         $metaFlag = $request->getPost('metaFlag');        
         if ($metaFlag == "yes") {
            return $this->redirect()->toRoute('plans', array('action'=>'addplanmeta'));
         }
         else {
            // process the submit and save draft button
            if ($button == "formSavePlan" || $button == "formSaveDraft") {
   
               // set the draft flag
               $draftFlag = $this->getDraftFlag($button);
               
               // get form data    
               $assessmentMethod = trim($request->getPost('textAssessmentMethod'));
               $population = trim($request->getPost('textPopulation'));
               $sampleSize = trim($request->getPost('textSamplesize'));
               $assessmentDate = trim($request->getPost('textAssessmentDate'));
               $cost = trim($request->getPost('textCost'));
               $analysisType = trim($request->getPost('textAnalysisType'));
               $administrator = trim($request->getPost('textAdministrator'));
               $analysisMethod = trim($request->getPost('textAnalysisMethod'));
               $scope = trim($request->getPost('textScope'));
               $feedback = trim($request->getPost('textFeedback'));
               $feedbackFlag = trim($request->getPost('textFeedbackFlag'));
               $planStatus = trim($request->getPost('textPlanStatus'));
                                
               // insert into plan table and obtain the primary key of the insert
               $planId = $this->getDatabaseData()->insertPlan(0, "", $year, $assessmentMethod,$population,$sampleSize,$assessmentDate,$cost,$analysisType,$administrator,$analysisMethod,$scope,$feedback,$feedbackFlag,$planStatus,$draftFlag,$this->userID);                  
   
               // insert one entry for each outcome id selected              
               foreach ($outcomeIds as $outcomeId) :
                  // insert into the outcome table
                  $this->getDatabaseData()->insertPlanOutcome($outcomeId, $planId);
               endforeach;
               
               // upload the files
               $data = array_merge_recursive(
                  $this->getRequest()->getPost()->toArray(),
                  $this->getRequest()->getFiles()->toArray()
               );

               // set the form data
               $form->setData($data);
               
               if ($form->isValid()) {

                  // get the data from the upload form
                  $data = $form->getData();
                  
                  /*
                   * create an array of file information
                   *  1) name
                   *  2) type
                   *  3) tmp_name - server location & filename
                   *  4) error
                   *  5) size
                   */              
                  $fileCollection = $data['file-collection'];
                  
                  // loop through each element and create an array of file names with extensions                 
                  foreach ($fileCollection as $file) :
                      if (!empty($file['name'])) {  
                        $fileNames[] = $file['name'];
                      }
                  endforeach;
                  
                  //get the file description
                  $fileDescription = $data['text'];
                                    
                  // insert into the plans document table by looping through the file name array
                  foreach ($fileNames as $fileName) :
                     $this->getDatabaseData()->insertPlanDocuments($planId, $fileName, $fileDescription);
                  endforeach;
                                      
                  return $this->redirect()->toRoute('plans');
               }
            }
         }
      }
      else {
         // process the get request
         
         // get session data
         $planSession = new Container('planSession');
         $action = $planSession->action; 
         $unit = $planSession->unit;
         $programs = $planSession->programs;
         $year = $planSession->year;
         $useractions = $planSession->useractions;
                  
         // Initial Page Load, get request
         // get units
         $unitarray[] = $this->getInitialUnits();

         // get years
	 $yeararray[] = $this->getInitialYears();    

         // get an array of outcome entities for each program
         // the outcomes array is an array of entity arrays
         foreach ($programs as $data) :
            $dbData = $this->getGenericQueries()->getUniqueOutcomes($unit, $data, $year);
            $outcomes[] = $dbData;
         endforeach;
               
         // Initial Page Load, get request
         return new ViewModel(array(
            'form' => $form ,
            'units' => $unitarray,
            'years' => $yeararray,
                
            'action' => $action,
            'unit' => $unit,
            'programs' => $programs,
            'year' => $year,
            'useractions' => $useractions,
            'outcomes' => $outcomes,
         ));
      }
   }
    
   /**
    * Controller used for the add plan meta page
    */
   public function addplanmetaAction()
   {
               
      $request = $this->getRequest();
      if ($request->isPost()) {
         // process post request
         
         // get session data
       	 $planSession = new Container('planSession');	 
         $year = $planSession->year;
         
         // get button from form
         $button = $request->getPost('formSubmitMeta');
                                
         // process the submit and save draft form                                  
         if ($button == "formSavePlan" || $button == "formSaveDraft") {

            $draftFlag = $this->getDraftFlag($button);
          
            // get form data    
            $metaDescription = $request->getPost('textMetaDescription');
                                 
            // insert into plan table and obtain the primary key of the insert
            $planId = $this->getDatabaseData()->insertPlan(1, $metaDescription, $year, "","","","","","","","","","","","",$draftFlag);                  
              
            // insert into the meta plans table
            // TODO SCOTT
            //$this->getDatabaseData()->insertPlanOutcome($outcomeId, $planId['maxId']);
                     
            return $this->redirect()->toRoute('plans');
         }
      }
      else {
         // process the get request
         
         // get session data
       	 $planSession = new Container('planSession');
	 $action = $planSession->action; 
         $unit = $planSession->unit;
         $programs = $planSession->programs;
         $year = $planSession->year;
         $outcomeId = $planSession->outcomeId;
         $useractions = $planSession->useractions;
         
         // Initial Page Load, get request
         // get units
         $unitarray[] = $this->getInitialUnits();

         // get years
	 $yeararray[] = $this->getInitialYears();
            
         // Initial Page Load, get request
         return new ViewModel(array(
            'units' => $unitarray,
            'years' => $yeararray,
               
            'action' => $action,
            'unit' => $unit,
            'programs' => $programs,
            'year' => $year,
            'useractions' => $useractions,
            'outcomeId' => $outcomeId,
         ));
      }
   }
     
   /**
    * Private Class function to get all the initial departments
    *
    * Return an array of all the departments
    */
   private function getInitialUnits()
   {
      // get units
      $results = $this->getGenericQueries()->getUnits();
      // iterate over database results forming a php array
      foreach ($results as $result){
         $unitarray[] = $result;
      }
      return $unitarray;
   }

   /**
    * Private Class function to get all the initial years
    *
    * Return an array of all the years
    */
   private function getInitialYears()
   {
      // get all years
      $results = $this->getGenericQueries()->getYears();
      
      // iterate through results forming a php array
      foreach ($results as $result){
         $yearsarray[] = $result;
      }
            
      return $yearsarray;
   }
   
   /**
    * Set and return the draft flag based on the type of button pressed
    */
   private function getDraftFlag($button)
   {
      // set the draft flag
      $draftFlag = 0;
      if ($button == "formSaveDraft") {
         $draftFlag = 1;
      }
      return $draftFlag;
   }
}
