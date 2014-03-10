<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Admin\Controller;


use Admin\Model\Queries;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Adapter;
use Zend\Paginator\Adapter\ArrayAdapter;
use Zend\Paginator\Paginator;
use Zend\Session\Container;
use Application\Authentication\AuthUser;

class QueriesController extends AbstractActionController
{
   protected $tableResults;
   
   public function onDispatch(\Zend\Mvc\MvcEvent $e) 
   {    $validUser = new AuthUser();
        if (!$validUser->Validate()){
            return $this->redirect()->toRoute('application');
        }
        else{
            return parent::onDispatch( $e );
        }
   }

   public function indexAction()
   {
       return new ViewModel();
   }
 
   // Show programs that don't have any plans for a specific year 
   public function getQuery1Action()
   {
      $resultsarray = '';
      
      // get year from route
      $year = $this->params()->fromRoute('id', 0);
      
      $results = $this->getUserQueries()->getProgramsMissingPlansForYear($year);
      
      // iterate over database results forming a php array
      foreach ($results as $result){
          $resultsarray[] = $result;
      }

      $partialView = new ViewModel(array('querytitle' => 'Programs Missing Plans For ' . $year,
                                         'programs' => $resultsarray));
      $partialView->setTerminal(true);
      return $partialView;
   }
   
   // Show programs that don't have any reports for a specific year 
   public function getQuery2Action(){
      
      $resultsarray = '';
      
      // get year from route
      $year = $this->params()->fromRoute('id', 0);
      
      $results = $this->getUserQueries()->getProgramsMissingReportsForYear($year);
      
      // iterate over database results forming a php array
      foreach ($results as $result){
          $resultsarray[] = $result;
      }

      $partialView = new ViewModel(array('querytitle' => 'Programs Missing Reports For ' . $year,
                                         'programs' => $resultsarray));
      $partialView->setTerminal(true);
      return $partialView;
   }
   
   // Show programs that are conducting meta assessment
   public function getQuery3Action(){
      
      $resultsarray = '';
      
      // get year from route
      $year = $this->params()->fromRoute('id', 0);
      
      $results = $this->getUserQueries()->getProgramsDoingMetaAssessment($year);
      
      // iterate over database results forming a php array
      foreach ($results as $result){
          $resultsarray[] = $result;
      }

      $partialView = new ViewModel(array('querytitle' => 'Programs Conducting Meta Assessment For ' . $year,
                                         'programs' => $resultsarray));
      $partialView->setTerminal(true);
      return $partialView;
   }
   
   // Show programs that are requesting funding
   public function getQuery4Action(){
      
      $resultsarray = '';
      
      // get year from route
      $year = $this->params()->fromRoute('id', 0);
      
      $results = $this->getUserQueries()->getProgramsNeedingFunding($year);
      
      // iterate over database results forming a php array
      foreach ($results as $result){
          $resultsarray[] = $result;
      }

      $partialView = new ViewModel(array('querytitle' => 'Programs Requesting Funding For ' . $year,
                                         'programs' => $resultsarray));
      $partialView->setTerminal(true);
      return $partialView;
   }
   
   // Show programs with modified outcomes
   public function getQuery5Action(){
      
      $resultsarray = '';
      
      // get year from route
      $fromDate = $this->params()->fromRoute('id', 0);

      $results = $this->getUserQueries()->getProgramsWithModifiedOutcomes($fromDate);
      
      // iterate over database results forming a php array
      foreach ($results as $result){
          $resultsarray[] = $result;
      }
      // format date for output
      $fromDate = substr($fromDate, 0, 2) . '-' .
                  substr($fromDate, 2, 2) . '-' .
                  substr($fromDate, 4);

      $partialView = new ViewModel(array('querytitle' => 'Programs With Modified Outcomes Since ' . $fromDate,
                                         'programs' => $resultsarray));
      $partialView->setTerminal(true);
      return $partialView;
   }
   
   // Show programs that have added or modified a report for a previous year
   public function getQuery6Action(){
      
      $resultsarray = '';
      
      // determine current school year
      date_default_timezone_set('America/Chicago');
      $currentMonth = date('m', time());
      $currentYear = date('Y', time());
      
      // determine current school year
      // year in plans table is spring term year (2013-2014 school year is entered as 2014)
      if ($currentMonth > 8){
         $currentYear = $currentYear + 1;
      }
      $results = $this->getUserQueries()->getProgramsModifiedLastYearsPlans($currentYear);
      
      // iterate over database results forming a php array
      foreach ($results as $result){
          $resultsarray[] = $result;
      }

      $partialView = new ViewModel(array('querytitle' => 'Programs That Modified Last Year\'s Plans ',
                                         'programs' => $resultsarray));
      $partialView->setTerminal(true);
      return $partialView;
   }
   
   // Show programs that have added or modified a report for a previous year
   public function getQuery7Action(){
      
      $resultsarray = '';
      
      // determine current school year
      date_default_timezone_set('America/Chicago');
      $currentMonth = date('m', time());
      $currentYear = date('Y', time());
      
      // determine current school year
      // year in plans table is spring term year (2013-2014 school year is entered as 2014)
      if ($currentMonth > 8){
         $currentYear = $currentYear + 1;
      }
      $results = $this->getUserQueries()->getProgramsModifiedLastYearsReports($currentYear);
      
      // iterate over database results forming a php array
      foreach ($results as $result){
          $resultsarray[] = $result;
      }

      $partialView = new ViewModel(array('querytitle' => 'Programs That Modified Last Year\'s Reports ',
                                         'programs' => $resultsarray));
      $partialView->setTerminal(true);
      return $partialView;
   }
   
   // Show programs that have not yet been reviewed by liaisons
   public function getQuery8Action(){
      
      $resultsarray = '';
      
      // get year from route
      $year = $this->params()->fromRoute('id', 0);
      
      $results = $this->getUserQueries()->getProgramsNeedingFeedback($year);
      
      // iterate over database results forming a php array
      foreach ($results as $result){
          $resultsarray[] = $result;
      }

      $partialView = new ViewModel(array('querytitle' => 'Programs Needing Feedback For ' . $year,
                                         'programs' => $resultsarray));
      $partialView->setTerminal(true);
      return $partialView;
   }
   
   // Show programs that have changed their assessors
   public function getQuery9Action(){
      
      $resultsarray = '';
      
      // get year from route
      $fromDate = $this->params()->fromRoute('id', 0);
      
      $results = $this->getUserQueries()->getProgramsWhoChangedAssessors($fromDate);
      
      // iterate over database results forming a php array
      foreach ($results as $result){
          $resultsarray[] = $result;
      }

       // format date for output
      $fromDate = substr($fromDate, 0, 2) . '-' .
                  substr($fromDate, 2, 2) . '-' .
                  substr($fromDate, 4);
      
      $partialView = new ViewModel(array('querytitle' => 'Programs That Changed Assessors For ' . $fromDate,
                                         'programs' => $resultsarray));
      $partialView->setTerminal(true);
      return $partialView;
   }

   // establishes the dbadapter link for all user queries
    public function getUserQueries()
    {
      if (!$this->tableResults) {
         $this->tableResults = $this->getServiceLocator()
                                    ->get('Admin\Model\Queries');
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
   
}
