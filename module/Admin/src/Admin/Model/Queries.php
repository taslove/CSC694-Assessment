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
    
    // query 1
    public function getProgramsMissingPlansForYear($year)
    {
        $sql = new Sql($this->adapter);
        
        // get programs that have a plan
        $select1 = $sql->select()
                      ->from('programs')
                      ->columns(array('id'))
                      ->join('plan_programs', 'plan_programs.program_id = programs.id',array())
                      ->join('plans', 'plans.id = plan_programs.plan_id',array())
                      ->where(array('plans.year' => $year))
        ;
        // get programs that are not in the set above
        $select2 = $sql->select()
                       ->from('programs')
                       ->columns(array('id', 'unit_id', 'name'))
                       ->where(new NotIn('programs.id', $select1))
                       ->where(array('programs.active_flag' => 1))
        ;
        
        // Find all programs that do not have plans for the year
        $statement = $sql->prepareStatementForSqlObject($select2);
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
        
        // get programs that have a plan for the selected year but are not in above set
        $select = $sql->select()
                      ->from('programs')
                      ->columns(array('unit_id', 'name'))
                      ->join('plan_programs', 'plan_programs.program_id = programs.id',array())
                      ->join('plans', 'plans.id = plan_programs.plan_id',array('id'))
                      ->where(array('plans.year' => $year))
                      ->where(new NotIn('plans.id', $reportsselect))
                      
        ;
        
        
        // find all programs with missing reports
        $statement = $sql->prepareStatementForSqlObject($select);
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
                      ->join('plan_programs', 'plan_programs.program_id = programs.id', array())
                      ->join('plans', 'plans.id = plan_programs.plan_id', array())
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
        
        // get programs requesting funding for plans 
        $select = $sql->select()
                      ->from('programs')
                      ->columns(array('unit_id', 'name'))
                      ->join('plan_programs', 'plan_programs.program_id = programs.id',array())
                      ->join('plans', 'plans.id = plan_programs.plan_id',array())
                      ->where(array('plans.year' => $year))
                      ->where(array('plans.funding_flag' => 1))
        ;
        
        $statement = $sql->prepareStatementForSqlObject($select);
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
                      ->join('users', 'users.id = outcomes.deactivated_user', array('last_name', 'first_name'))
                      ->where($where->isNotNull('outcomes.deactivated_ts'))
                      ->where($where->greaterThan('outcomes.deactivated_ts', $fromDate))
        ;
                
        // find all programs requesting fundingconducting meta assessment
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
      
        return $result;
    }
    
    // query 6
    public function getProgramsModifiedLastYearsPlans($currentYear)
    {
        $sql = new Sql($this->adapter);
    
        $previousYear = $currentYear - 1;
        $where = new Where();
         // get programs that have a modified timestamp of this year but plan year previous year
        $select = $sql->select()
                      ->from('programs')
                      ->columns(array('unit_id', 'name'))
                      ->quantifier(\Zend\Db\Sql\Select::QUANTIFIER_DISTINCT)
                      ->join('plan_programs', 'plan_programs.program_id = programs.id',array())
                      ->join('plans', 'plans.id = plan_programs.plan_id',array())
                      // instantiating new where must come first
                      ->where($where->like('plans.modified_ts', $currentYear . '%'))
                      ->where(array('plans.year' => $previousYear))
                   
        ; 
        
        // find all programs requesting fundingconducting meta assessment
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
      
        return $result;
    }
    
    // query 7
    public function getProgramsModifiedLastYearsReports($currentYear)
    {
        $sql = new Sql($this->adapter);
    
        $previousYear = $currentYear - 1;
        $where = new Where();
         // get programs that have a modified timestamp of this year but plan year previous year
        $select = $sql->select()
                      ->from('programs')
                      ->columns(array('unit_id', 'name'))
                      ->quantifier(\Zend\Db\Sql\Select::QUANTIFIER_DISTINCT)
                      ->join('plan_programs', 'plan_programs.program_id = programs.id',array())
                      ->join('plans', 'plans.id = plan_programs.plan_id',array())
                      ->join('reports', 'reports.plan_id = plans.id',array())
                      // instantiating new where must come first
                      ->where($where->like('reports.modified_ts', $currentYear . '%'))
                      ->where(array('plans.year' => $previousYear))
                   
        ; 
        
        // find all programs requesting fundingconducting meta assessment
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
      
        return $result;
    }
    
}