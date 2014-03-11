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
use Zend\View\Model\JsonModel;
use Zend\Session\Container;

use Outcomes\Form\OutcomesForm;
use Outcomes\Model\Outcomes;

class OutcomesController extends AbstractActionController
{ 
    protected $tableResults;
    // get these values from the session namespace
    protected $userRole = 3;
    protected $userID = 9;
    
   /*
   $namespace = new Container('user');  
   $namespace->userID;   
             ->userEmail;
             ->role;            if 0, no admin abilities
             ->datatelID;     if null, redirect to login screen
    
    do this check on every page
    if (datatelID == null) redirect to Application
    
   */
        
   public function indexAction(){
      $results = $this->getGenericQueries()->getUnits();
      // iterate over database results forming a php array
      foreach ($results as $result){
         $unitarray[] = $result;
      }
      return new ViewModel(array(
                'units' => $unitarray,
                'userRole' => $this->userRole,
                'userID' => $this->userID,
      ));      
   }
    
   // Creates list of available units (departments/programs)
   // based on user role and privileges.
   public function getUnitsAction(){      
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
    
   public function getProgramsAction(){
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
    
   public function getOutcomesAction(){
      // get program that's selected from id in url
      $programSelected = $this->params()->fromRoute('id', 0);
      $request = $this->getRequest();
         
      // this code will execute if an outcome was just added, edited or deactivated
      if ($request->isPost()) {
            
         // handle an add
         if ($request->getPost('action') == "add"){
            // get outcome text from post data and use it to create outcome
            $outcomeText = $request->getPost('outcomeText');
            $this->getOutcomesQueries()->addOutcome($programSelected, $outcomeText, $this->userID);
         }
            
         // handle an edit
         else if ($request->getPost('action') == "edit"){
            $oidToDeactivate = $request->getPost('oidToDeactivate');
            $outcomeText = $request->getPost('outcomeText');
            $this->getOutcomesQueries()->editOutcome($programSelected, $outcomeText, $oidToDeactivate, $this->userID); 
         }
            
         // handle a delete / deactivate
         else {
            $outcomeId = $request->getPost('oid');
            $this->getOutcomesQueries()->deactivateOutcome($outcomeId, $this->userID);
         }
      }
      // get outcomes for the selected program
      $results = $this->getOutcomesQueries()->getAllActiveOutcomesForProgram($programSelected);
      
      $partialView = new ViewModel(array(
         'outcomes' => $results,
         'programId' => $programSelected,
      ));
      // ignore the layout template
      $partialView->setTerminal(true);
      return $partialView;
   }
   
   
   public function addOutcomeAction()
   {
      // get programs from id in url
      $programChosen = $this->params()->fromRoute('id', 0);
         
      // render the addOutcome screen
      $partialView = new ViewModel(array(
         'programChosen' => $programChosen,
      ));
      // ignore the layout template
      $partialView->setTerminal(true);
      return $partialView;
   }
   
   public function editOutcomeAction(){
      // get programs from id in url
      $programChosen = $this->params()->fromRoute('id', 0);
      
      // need this to pull out POST data
      $request = $this->getRequest();
      
      // get the outcome id from post data then get the outcome from the id
      $outcomeId = $request->getPost('oid');
      $outcomeText = $request->getPost('text');

      $partialView = new ViewModel(array(
         'outcomeId' => $outcomeId,
         'outcomeText' => $outcomeText,
         'programChosen' => $programChosen,
      ));
      
      // ignore the layout template
      $partialView->setTerminal(true);
      return $partialView;
   }
   

   public function getGenericQueries()
   {
      if (!$this->tableResults){
         $this->tableResults = $this->getServiceLocator()
                                       ->get('Application\Model\AllTables');                   
      }
      return $this->tableResults;
   }
    
   public function getOutcomesQueries()
   {
      if (!$this->tableResults) {
         $this->tableResults = $this->getServiceLocator()
                       ->get('Outcomes\Model\OutcomesTable');                
      }
      return $this->tableResults;
   }
}
