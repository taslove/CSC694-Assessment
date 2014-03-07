<?php

namespace Admin\Model;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Predicate\NotIn;
use Zend\Db\Sql\Expression;


// This class must appear in the Module.php file in this module.

class Queries extends AbstractTableGateway
{
    public $adapter;
    
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }
    public function getTest($userID){
         // get units available for user from unit_privs and liaison_privs
        $sql = new Sql($this->adapter);
        
        //              ->where("programs.unit_id != 'GEN' ")
        
        // check session namespace role
        // if admin, get all units
        
        $select1 = $sql->select()
                    ->from('unit_privs')
                    ->columns(array('id' => 'unit_id'))
                    ->where(array('unit_privs.user_id' => $userID));
        
        
        $select2 = $sql->select()
                    ->from('liaison_privs')
                    ->columns(array('id' => 'unit_id'))
                    ->where(array('liaison_privs.user_id' => $userID));
        
        // union results from both selects
        $select1->combine($select2);
 

        $statement = $sql->prepareStatementForSqlObject($select1);
        $result = $statement->execute();
        
        // dumping $result will not show any rows returned
        // you must iterate over $result to retrieve query results
        
        return $result;
    }
    
    // query 1
    public function getProgramsMissingPlansForYear($year)
    {
        $sql = new Sql($this->adapter);
        
        // get programs that have an outcomes plan for the selected year
        $subselect1 = $sql->select()
                      ->from('programs')
                      ->columns(array('id'))
                      ->join('outcomes', 'outcomes.program_id = programs.id',array())
                      ->join('plan_outcomes', 'plan_outcomes.outcome_id = outcomes.id', array())
                      ->join('plans', 'plans.id = plan_outcomes.plan_id',array())
                      ->where(array('plans.year' => $year))
        ;
        
        // get programs that have a meta-assessment plan for the selected year
        $subselect2 = $sql->select()
                      ->from('programs')
                      ->columns(array('id'))
                      ->join('meta_plans', 'meta_plans.program_id = programs.id',array())
                      ->join('plans', 'plans.id = meta_plans.plan_id',array())
                      ->where(array('plans.year' => $year))
        ;
       
        // get programs that are not in any of the sets above
        $select = $sql->select()
                       ->from('programs')
                       ->columns(array('id', 'unit_id', 'name'))
                       ->where(new NotIn('programs.id', $subselect1))
                       ->where(new NotIn('programs.id', $subselect2))
                       ->where(array('programs.active_flag' => 1))
        ;
        
        // Find all programs that do not have plans for the year, excluding Gen Ed
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        
        // dumping $result will not show any rows returned
        // you must iterate over $result to retrieve query results
        
        return $result;
    }
    
    // query 2
    public function getProgramsMissingReportsForYear($year)
    {
        $sql = new Sql($this->adapter);
        
        // get plan ids for all reports
        $reportsselect = $sql->select()
                            ->from('reports')
                            ->columns(array('plan_id'))
        ;
        
        // get programs that have an outcomes plan for the selected year but no report
        $subselect1 = $sql->select()
                      ->from('programs')
                      ->columns(array('unit_id', 'name'))
                      ->join('outcomes', 'outcomes.program_id = programs.id',array())
                      ->join('plan_outcomes', 'plan_outcomes.outcome_id = outcomes.id', array())
                      ->join('plans', 'plans.id = plan_outcomes.plan_id',array('id'))
                      ->where(array('plans.year' => $year))
                      ->where(new NotIn('plans.id', $reportsselect))
                      
        ;
        
        // get programs that have a meta-assessment plan for the selected year but no report
        $subselect2 = $sql->select()
                      ->from('programs')
                      ->columns(array('unit_id', 'name'))
                      ->join('meta_plans', 'meta_plans.program_id = programs.id',array())
                      ->join('plans', 'plans.id = meta_plans.plan_id',array('id'))
                      ->where(array('plans.year' => $year))
                      ->where(new NotIn('plans.id', $reportsselect))
        ;
       
        $subselect1->combine($subselect2);
        
        // find all programs and the plan id with missing reports
        $statement = $sql->prepareStatementForSqlObject($subselect1);
        $result = $statement->execute();
        
        return $result;
    }
    
    // query 3
    public function getProgramsDoingMetaAssessment($year)
    {
        $sql = new Sql($this->adapter);
        
        // get programs that have a meta-assessment plan for the selected year but no report
        $select = $sql->select()
                      ->from('programs')
                      ->columns(array('unit_id', 'name'))
                      ->join('meta_plans', 'meta_plans.program_id = programs.id',array())
                      ->join('plans', 'plans.id = meta_plans.plan_id',array('id'))
                      ->where(array('plans.year' => $year))
        ;
       
        // find all programs conducting meta assessment
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        
        return $result;
    }
    
    // query 4
    public function getProgramsNeedingFunding($year)
    {
        $sql = new Sql($this->adapter);
        
        // get programs requesting funding on outcomes plans
        $select1 = $sql->select()
                      ->from('programs')
                      ->columns(array('unit_id', 'name'))
                      ->join('outcomes', 'outcomes.program_id = programs.id',array())
                      ->join('plan_outcomes', 'plan_outcomes.outcome_id = outcomes.id', array())
                      ->join('plans', 'plans.id = plan_outcomes.plan_id',array())
                      ->where(array('plans.year' => $year))
                      ->where(array('plans.funding_flag' => 1))
        ;
        // get programs requesting funding on meta-assessment plans
        $select2 = $sql->select()
                      ->from('programs')
                      ->columns(array('unit_id', 'name'))
                      ->join('meta_plans', 'meta_plans.program_id = programs.id',array())
                      ->join('plans', 'plans.id = meta_plans.plan_id',array())
                      ->where(array('plans.year' => $year))
                      ->where(array('plans.funding_flag' => 1))
        ;
   
        $select1->combine($select2);

        // find all programs requesting fundingconducting meta assessment
        $statement = $sql->prepareStatementForSqlObject($select1);
        $result = $statement->execute();
      
        return $result;
    }
    
    // query 5
    public function getProgramsWithModifiedOutcomes($fromDate)
    {
        $sql = new Sql($this->adapter);
        $where = new Where();
        $fromDate = '21-01-2014';  // dd-mm-yyyy
        $fromDate = date('Y-m-d H:i:s', strtotime($fromDate));
        
        // get programs that changed outcomes
        $select = $sql->select()
                      ->from('programs')
                      ->columns(array('unit_id', 'name'))
                      ->quantifier(\Zend\Db\Sql\Select::QUANTIFIER_DISTINCT)
                      ->join('outcomes', 'outcomes.program_id = programs.id',array())
                      ->where($where->isNotNull('outcomes.modified_ts'))
                      ->where($where->greaterThan('outcomes.modified_ts', $fromDate))
        ;
        // find all programs requesting fundingconducting meta assessment
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
      
        return $result;
    }
    
    // query 6
    public function getProgramsWithModifiedPreviousYearPlans($currentYear)
    {
        $sql = new Sql($this->adapter);
        
        $previousYear = $currentYear - 1;
        $where = new Where();
         // get programs that have an outcomes plan for the previous year 
        $subselect1 = $sql->select()
                      ->from('programs')
                      ->columns(array('unit_id', 'name'))
                      ->quantifier(\Zend\Db\Sql\Select::QUANTIFIER_DISTINCT)
                      ->join('outcomes', 'outcomes.program_id = programs.id',array())
                      ->join('plan_outcomes', 'plan_outcomes.outcome_id = outcomes.id', array())
                      ->join('plans', 'plans.id = plan_outcomes.plan_id',array('id'))
                      ->where(array('plans.year' => $previousYear))
                      ->where($where->like('plans.modified_ts',$currentYear . '%'))
                      
        ;
        $where = new Where();
        // get programs that have a meta-assessment plan for the selected year but no report
        $subselect2 = $sql->select()
                      ->from('programs')
                      ->columns(array('unit_id', 'name'))
                      ->quantifier(\Zend\Db\Sql\Select::QUANTIFIER_DISTINCT)
                      ->join('meta_plans', 'meta_plans.program_id = programs.id',array())
                      ->join('plans', 'plans.id = meta_plans.plan_id',array('id'))
                      ->where(array('plans.year' => $previousYear))
                      ->where($where->like('plans.modified_ts',$currentYear . '%'))
        ;
        echo var_dump($subselect1->getSqlString());
        $subselect1->combine($subselect2);

        ;
        // find all programs requesting fundingconducting meta assessment
        $statement = $sql->prepareStatementForSqlObject($subselect1);
        $result = $statement->execute();
      
        return $result;
    }
    
}