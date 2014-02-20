<?php

namespace Application\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Expression;
use Plans\Model\Entity;


// This class must be included in the factories array in
// this modules Module.php

// This class holds the database method calls.
// The class variable $table references a base table used in the
// queries.  This variable is required in the class but is not required
// to be used in all database calls.  For example, the from clause could
// explicitly reference a table (->from('student')).

class AllTables extends AbstractTableGateway
{
    public $adapter;
    
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }
  
    // Retrieves all active units
    public function getUnits()
    {   
        $sql = new Sql($this->adapter);
        $select = $sql->select()
                      ->from('units')
                      ->where(array('active' => 1));
                      
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        
        // dumping $result will not show any rows returned
        // you must iterate over $result to retrieve query results
        
        return $result;
    }
    
    // Retrieves all active programs for a given unit id
    public function getProgramsByUnitId($unitid)
    {   
        $sql = new Sql($this->adapter);
        $select = $sql->select()
                      ->from('programs')
                      ->where(array('unit_id' => $unitid))
                      ->where(array('active' => 1));
                      
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        
        // dumping $result will not show any rows returned
        // you must iterate over $result to retrieve query results
        
        return $result;
    }
    
    
    
    
    
    
    
    // TODO-Scott Added, these all need to go to the plans model - BEGIN
    public function getYears()
    {
/*    
SELECT year from assessment.plans
group by year
;
 */   
        
        $sql = new Sql($this->adapter);
        $select = $sql->select()
                      ->columns(array('year' => new Expression('plans.year')))
                      ->from('plans')
		      ->group (array('year' => new Expression('plans.year')))
		   ;

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        
        return $result;
    }
    
    public function getPlans($unit_id, $names, $year)
    {
        
/*
SELECT pl.id from assessment.units un
	inner join assessment.programs p
		on p.unit_id = un.id
    inner join assessment.outcomes o
		on o.program_id = p.id
	inner join assessment.plan_outcomes po
		on po.outcome_id = o.id
	inner join assessment.plans pl
		on pl.id = po.plan_id
where un.id = 'CSC'
	and p.name = 'BS Computer Science'
	and pl.year = 2011
group by pl.id
;
*/
        $sql = new Sql($this->adapter);
        $select = $sql->select()
                      ->columns(array('planId' => new Expression('plans.id')))
                      ->from('units')
		      ->join('programs', 'programs.unit_id = units.id')
		      ->join('outcomes', 'outcomes.program_id = programs.id')
		      ->join('plan_outcomes', 'plan_outcomes.outcome_id = outcomes.id')
		      ->join('plans', 'plans.id = plan_outcomes.plan_id')		      
		      //->where(array('units.id' => $unit_id, 'programs.name' => array('BA Computer Science','BS Computer Science'), 'plans.year' => $year))
                      ->where(array('units.id' => $unit_id, 'programs.name' => $names, 'plans.year' => $year))
		      ->group (array('planId' => new Expression('plans.id')))
		   ;

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        return $result;
    }
    
    public function getOutcomes($unit_id, $names, $year)
    {
/*
SELECT o.id, pl.id, o.outcome_text from assessment.units un
	inner join assessment.programs p
		on p.unit_id = un.id
    inner join assessment.outcomes o
		on o.program_id = p.id
	inner join assessment.plan_outcomes po
		on po.outcome_id = o.id
	inner join assessment.plans pl
		on pl.id = po.plan_id
where un.id = 'CSC'
	and p.name = 'BS Computer Science'
	and pl.year = 2011
group by o.id, pl.id, o.outcome_text
;
*/	
        $sql = new Sql($this->adapter);
        $select = $sql->select()
                      ->columns(array('outcomeId' => new Expression('outcomes.id'),
				      'planId' => new Expression('plans.id'),
				      'outcomeText' => new Expression('outcomes.outcome_text'),
				      ))
                      ->from('units')
		      ->join('programs', 'programs.unit_id = units.id')
		      ->join('outcomes', 'outcomes.program_id = programs.id')
		      ->join('plan_outcomes', 'plan_outcomes.outcome_id = outcomes.id')
		      ->join('plans', 'plans.id = plan_outcomes.plan_id')		      
		      ->where(array('units.id' => $unit_id, 'programs.name' => $names, 'plans.year' => $year))
		      ->group (array('outcomeId' => new Expression('outcomes.id'),
				     'planId' => new Expression('plans.id'),
				     'outcomeText' => new Expression('outcomes.outcome_text'),
				      ))
		   ;

        $statement = $sql->prepareStatementForSqlObject($select);
        $resultSet = $statement->execute();

	$entities = array();
        foreach ($resultSet as $row) {
            $entity = new Entity\Outcome($row['outcomeId'],$row['planId'],$row['outcomeText']);
            $entities[] = $entity;
        }
        return $entities;
    }
    
    public function getOutcomesByPlanId($planId)
    {
/*
SELECT pl.id, o.outcome_text from assessment.outcomes o	
	inner join assessment.plan_outcomes po
		on po.outcome_id = o.id
	inner join assessment.plans pl
		on pl.id = po.plan_id
where pl.id = 1175
group by pl.id, o.outcome_text
;
*/	
	
        $sql = new Sql($this->adapter);
        $select = $sql->select()
                      ->columns(array('outcomeId' => new Expression('outcomes.id'),
				      'planId' => new Expression('plans.id'),
				      'outcomeText' => new Expression('outcomes.outcome_text'),
				      ))
                      ->from('outcomes')
		      ->join('plan_outcomes', 'plan_outcomes.outcome_id = outcomes.id')
		      ->join('plans', 'plans.id = plan_outcomes.plan_id')		      
		      ->where(array('plans.id' => $planId))
		      ->group (array('outcomeId' => new Expression('outcomes.id'),
				     'planId' => new Expression('plans.id'),
				     'outcomeText' => new Expression('outcomes.outcome_text'),
				      ))
		   ;

        $statement = $sql->prepareStatementForSqlObject($select);
        $resultSet = $statement->execute();

	$entities = array();
        foreach ($resultSet as $row) {
            $entity = new Entity\Outcome($row['outcomeId'],$row['planId'],$row['outcomeText']);
            $entities[] = $entity;
        }
        return $entities;
    }
    
    
    public function getPlanByPlanId($planId)
    {
/*
select * from assessment.plans
where id = 1175
;
*/
        $sql = new Sql($this->adapter);
        $select = $sql->select()
                      ->from('plans')
		      ->where(array('id' => $planId))
		   ;

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
	
	// create and return  a single row
	$row = $result->current();   
        return $row;
    }
    
        public function getUniqueOutcomes($unit_id, $names, $year)
    {
/*
SELECT o.id, o.outcome_text from assessment.units un
	inner join assessment.programs p
		on p.unit_id = un.id
    inner join assessment.outcomes o
		on o.program_id = p.id
	inner join assessment.plan_outcomes po
		on po.outcome_id = o.id
	inner join assessment.plans pl
		on pl.id = po.plan_id
where un.id = 'CSC'
	and p.name = 'BS Computer Science'
	and pl.year = 2011
group by o.id, o.outcome_text
;
*/	
                
        $sql = new Sql($this->adapter);
        $select = $sql->select()
                      ->columns(array('outcomeId' => new Expression('outcomes.id'),
				      'outcomeText' => new Expression('outcomes.outcome_text'),
				      ))
                      ->from('units')
		      ->join('programs', 'programs.unit_id = units.id')
		      ->join('outcomes', 'outcomes.program_id = programs.id')
		      ->join('plan_outcomes', 'plan_outcomes.outcome_id = outcomes.id')
		      ->join('plans', 'plans.id = plan_outcomes.plan_id')		      
		      ->where(array('units.id' => $unit_id, 'programs.name' => $names, 'plans.year' => $year))
		      ->group (array('outcomeId' => new Expression('outcomes.id'),
				     'outcomeText' => new Expression('outcomes.outcome_text'),
				      ))
		   ;

        $statement = $sql->prepareStatementForSqlObject($select);
        $resultSet = $statement->execute();

	$entities = array();
        foreach ($resultSet as $row) {
            $entity = new Entity\Outcome($row['outcomeId'], 0,$row['outcomeText']);
            $entities[] = $entity;
        }
        return $entities;
    }

    // TODO-Scott Added, these all need to go to the plans model - END
        
        
        
  
}