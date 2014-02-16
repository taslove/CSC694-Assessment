<?php

namespace Plans\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Update;
use Zend\Db\Sql\Expression;

class DatabaseSql extends AbstractTableGateway
{
    protected $table = 'users';
    public $adapter;

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }
  
  
// Sample dump used in debugging, used as needed    
//        foreach ($result as $data) :
//            var_dump($data);
//        endforeach;
//        exit();
    
    
        public function getPlans($unit_id, $name, $year)
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
		      ->where(array('units.id' => $unit_id, 'programs.name' => $name, 'plans.year' => $year))
		      ->group (array('planId' => new Expression('plans.id')))
		   ;

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        return $result;
    }	
	
    public function getOutcomes($unit_id, $name, $year)
    {
/*
SELECT pl.id, o.outcome_text from assessment.units un
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
group by pl.id, o.outcome_text
;
*/	
	
        $sql = new Sql($this->adapter);
        $select = $sql->select()
                      ->columns(array('planId' => new Expression('plans.id'),
				      'outcomeText' => new Expression('outcomes.outcome_text'),
				      ))
                      ->from('units')
		      ->join('programs', 'programs.unit_id = units.id')
		      ->join('outcomes', 'outcomes.program_id = programs.id')
		      ->join('plan_outcomes', 'plan_outcomes.outcome_id = outcomes.id')
		      ->join('plans', 'plans.id = plan_outcomes.plan_id')		      
		      ->where(array('units.id' => $unit_id, 'programs.name' => $name, 'plans.year' => $year))
		      ->group (array('planId' => new Expression('plans.id'),
				     'outcomeText' => new Expression('outcomes.outcome_text'),
				      ))
		   ;

        $statement = $sql->prepareStatementForSqlObject($select);
        $resultSet = $statement->execute();

	$entities = array();
        foreach ($resultSet as $row) {
            $entity = new Entity\Outcome($row['planId'],$row['outcomeText']);
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
                      ->columns(array('planId' => new Expression('plans.id'),
				      'outcomeText' => new Expression('outcomes.outcome_text'),
				      ))
                      ->from('outcomes')
		      ->join('plan_outcomes', 'plan_outcomes.outcome_id = outcomes.id')
		      ->join('plans', 'plans.id = plan_outcomes.plan_id')		      
		      ->where(array('plans.id' => $planId))
		      ->group (array('planId' => new Expression('plans.id'),
				     'outcomeText' => new Expression('outcomes.outcome_text'),
				      ))
		   ;

        $statement = $sql->prepareStatementForSqlObject($select);
        $resultSet = $statement->execute();

	$entities = array();
        foreach ($resultSet as $row) {
            $entity = new Entity\Outcome($row['planId'],$row['outcomeText']);
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
    
    
    public function savePlan($id,$assessmentMethod,$population,$sampleSize,$assessmentDate,$cost,$analysisType,$administrator,$analysisMethod,$scope,$feedback,$feedbackFlag,$planStatus)
    {
/*
update assessment.plans
set meta_description = 'Hello'
where id = 1175
;
*/

        $sql = new Sql($this->adapter);
	$update = $sql->update()
			->table('plans')
			->set(array('meta_flag' => $metaFlag,
				    'meta_description' => $metaDecription,
				    'assessment_method' => $assessmentMethod,
				    'population' => $population,
				    'sample_size' => $sampleSize,
				    'assessment_date' => $assessmentDate,
				    'cost' => $cost,
				    'analysis_type' => $analysisType,
				    'administrator' => $administrator,
				    'analysis_method' => $analysisMethod,
				    'scope' => $scope,
				    'feedback' => $feedback,
				    'feedback_flag' => $feedbackFlag,
				    'plan_status' => $planStatus))
			->where(array('id' => $id))
		    ;
		    
        $statement = $sql->prepareStatementForSqlObject($update);
        $statement->execute();
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    public function getAllDepartments()
    {

/*    
    SELECT unit_id FROM assessment.user_roles ur
	inner join assessment.users u
		on ur.user_id = u.id
	inner join assessment.unit_privs up
		on u.id = up.user_id
    group by up.unit_id
    ;
 */   
        
        $sql = new Sql($this->adapter);
        $select = $sql->select()
                      ->columns(array('unit_id' => new Expression('unit_privs.unit_id')))
                      ->from('users')
                      ->join('user_roles', 'users.id = user_roles.user_id')
                      ->join('unit_privs', 'users.id = unit_privs.user_id')
                      ->group(new Expression('unit_privs.unit_id'))
		    ;

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();       
        
        return $result;
    }
    
    public function getAllPrograms()
    {

/*    
    SELECT p.name FROM assessment.user_roles ur
	inner join assessment.users u
		on ur.user_id = u.id
	inner join assessment.unit_privs up
		on u.id = up.user_id
	inner join assessment.units un
		on un.id = up.unit_id
	inner join assessment.programs p
		on p.unit_id = un.id
    group by p.name
;
 */   
        
        $sql = new Sql($this->adapter);
        $select = $sql->select()
                      ->columns(array('name' => new Expression('programs.name')))
                      ->from('users')
                      ->join('user_roles', 'users.id = user_roles.user_id')
                      ->join('unit_privs', 'users.id = unit_privs.user_id')
                      ->join('units', 'units.id = unit_privs.unit_id') //works
                      ->join('programs', 'programs.unit_id = units.id')
                      ->group(new Expression('programs.name'))
		    ;

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        return $result;
    }
    
    public function getSpecificPrograms($unit_id)
    {

/*    
    SELECT p.name FROM assessment.user_roles ur
	inner join assessment.users u
		on ur.user_id = u.id
	inner join assessment.unit_privs up
		on u.id = up.user_id
	inner join assessment.units un
		on un.id = up.unit_id
	inner join assessment.programs p
		on p.unit_id = un.id
    where un.id = 'ACC'
    group by p.name
;
 */
        
        
        $sql = new Sql($this->adapter);
        $select = $sql->select()
                      ->columns(array('name' => new Expression('programs.name')))
                      ->from('users')
                      ->join('user_roles', 'users.id = user_roles.user_id')
                      ->join('unit_privs', 'users.id = unit_privs.user_id')
                      ->join('units', 'units.id = unit_privs.unit_id')
                      ->join('programs', 'programs.unit_id = units.id')
                      //->where('users.id = 9') // works
                      //->where('units.id = \'ACC\'') // works                      
                      ->where(array('units.id' => $unit_id))
                      ->group(new Expression('programs.name'))
		    ;

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        return $result;
    }

    
    public function getYears($unit_id)
    {

/*    
SELECT pl.year from assessment.units un
	inner join assessment.programs p
		on p.unit_id = un.id
	inner join assessment.outcomes o
		on o.program_id = p.id
	inner join assessment.plan_outcomes po
		on po.outcome_id = o.id
	inner join assessment.plans pl
		on pl.id = po.plan_id
where un.id = 'CSC'
group by pl.year
;
 */   
        
        $sql = new Sql($this->adapter);
        $select = $sql->select()
                      ->columns(array('year' => new Expression('plans.year')))
                      ->from('units')
		      ->join('programs', 'programs.unit_id = units.id')
		      ->join('outcomes', 'outcomes.program_id = programs.id')
		      ->join('plan_outcomes', 'plan_outcomes.outcome_id = outcomes.id')
		      ->join('plans', 'plans.id = plan_outcomes.plan_id')
		      ->where(array('units.id' => $unit_id))
		      ->group (array('year' => new Expression('plans.year')))
		   ;

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        
        return $result;
    }
}