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

use Outcomes\Form\OutcomesForm;
use Outcomes\Model\Outcomes;

class OutcomesController extends AbstractActionController
{ 
    protected $tableResults;
    // get these values from the session namespace
    protected $userRole = 3;
    protected $userID = 9;
    
        
    public function indexAction()
    {      
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
    
   public function getOutcomesAction()
   {
         $request = $this->getRequest();
         
         // this would be the case if an outcome was just deleted
         if ($request->isPost()) {
      
            $this->getOutcomesQueries()->deactivateOutcome(5014);

         }
      
      
        // get programs from id in url
        $programChosen = $this->params()->fromRoute('id', 0);
        // get outcomes for that program
        $results = $this->getOutcomesQueries()->getAllActiveOutcomesForProgram($programChosen);
      
         $partialView = new ViewModel(array(
         'outcomes' => $results,
         'programId' => $programChosen,
      ));
      // ignore the layout template
      $partialView->setTerminal(true);
      return $partialView;
    }
   
   
   public function addOutcomeAction()
   {
      // get programs from id in url
      $programChosen = $this->params()->fromRoute('id', 0);
      $request = $this->getRequest();
      
      $addForm = new OutcomesForm();
      $addForm->get('submit')->setValue('Add');
      
      // set values that we already know
      $addForm->get('program_id')->setValue($programChosen);
      $addForm->get('active_flag')->setValue(1);
      
      // handle actually adding an outcome
        if ($request->isPost()) {
            $outcome = new Outcomes();
            $addForm->setInputFilter($outcome->getInputFilter());
            $addForm->setData($request->getPost());

            if ($addForm->isValid()) {
                $outcome->exchangeArray($addForm->getData());
                $this->getOutcomesQueries()->addOutcome($outcome);

                // Redirect to list of students
                return $this->redirect()->toRoute('outcomes');
            }
            else {
               $fail = "Validation fail";
               var_dump($addForm->getData());
               exit;
            }
        }  
      // if it's a get, we render the addOutcome screen
      $partialView = new ViewModel(array(
         'addForm' => $addForm,
      ));
      // ignore the layout template
      $partialView->setTerminal(true);
      return $partialView;
    }
   
   public function deactivateOutcome()
   {
      // get programs from id in url
      $outcomeId = $this->params()->fromRoute('id', 0);
    //  $this->getOutcomesQueries()->deactivateOutcome($outcomeId);
      
         // if it's a get, we render the addOutcome screen
      $partialView = new ViewModel(array(
      //   'addForm' => $addForm,
      ));
      // ignore the layout template
      $partialView->setTerminal(true);
      return $partialView;

      
   }
   
   // for testing purposes
      public function addAction()
    {
        $form = new OutcomesForm();
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        
        
        // this is called from outcomes/add
        if ($request->isPost()) {
            $outcome = new Outcomes();
            $form->setInputFilter($outcome->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $outcome->exchangeArray($form->getData());
                $this->getModelOutcomesTable()->saveOutcome($outcome);

                // Redirect to list of students
                return $this->redirect()->toRoute('outcomes');
            }
        }
        return array('form' => $form);
    }
   
   
    public function getGenericQueries()
    {
        if (!$this->tableResults) {
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
