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
    
    /*
    public function indexAction()
    {
        return new ViewModel(array(
            'outcomes' => $this->getModelOutcomesTable()->getAllOutcomes(),
            'units' => $this->getModelOutcomesTable()->getAllUnits(),
            'programs' => $this->getModelOutcomesTable()->getAllProgramsForUnit('ACC'),
            'filteredOutcomes' => $this->getModelOutcomesTable()->getAllOutcomesForProgram(1),
        ));
    }
    */

        
    public function indexAction()
    {
            
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
                'outcomes' => $this->getOutcomesQueries()->getAllOutcomes(), // just for testing
                'userRole' => $this->userRole,
                'userID' => $this->userID,
            ));
        }
        else{  // user in table with role - show actions
               // wait to populate units until action chosen
            return new ViewModel(array(
                'useractions' => array('View', 'Add', 'Modify'),
                'outcomes' => $this->getOutcomesQueries()->getAllOutcomesForProgram(2), // just for testing
                'userRole' => $this->userRole,
                'userID' => $this->userID,
            ));
        }
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
        // get programs from id in url
        $programChosen = $this->params()->fromRoute('id', 0);
        // get outcomes for that program
        $results = $this->getOutcomesQueries()->getAllOutcomesForProgram($programChosen);
      
        // iterate through results forming a php array
        foreach ($results as $result){
            $outcomesData[] = $result;
        }
      
        // encode results as json object
        $jsonData = new JsonModel($outcomesData);
        return $jsonData;
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
