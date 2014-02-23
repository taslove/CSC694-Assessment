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
    
    /**
     * Get all the unique years from the plans table
     */ 
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

    /**
     * Update a tuple on the plans table by id
     */
    public function updatePlan($id,$metaFlag,$metaDescription,$assessmentMethod,$population,$sampleSize,$assessmentDate,$cost,$analysisType,$administrator,$analysisMethod,$scope,$feedbackText,$feedbackFlag,$planStatus,$draftFlag,$userID)
    {
/*
update assessment.plans
set meta_description = 'Hello'
where id = 1175
;
*/
	// database timestamp format    
        //"1970-01-01 00:00:01";
        // gets the current timezone - date_default_timezone_get()
	
	// create the sytem timestamp
	$currentTimestamp = date("Y-m-d H:i:s", time());
      
        $sql = new Sql($this->adapter);
	$update = $sql->update()
			->table('plans')
			->set(array('modified_ts' => trim($currentTimestamp),
				    'meta_flag' => trim($metaFlag),
				    'meta_description' => trim($metaDecription),
				    'assessment_method' => trim($assessmentMethod),
				    'population' => trim($population),
				    'sample_size' => trim($sampleSize),
				    'assessment_date' => trim($assessmentDate),
				    'cost' => trim($cost),
				    'analysis_type' => trim($analysisType),
				    'administrator' => trim($administrator),
				    'analysis_method' => trim($analysisMethod),
				    'scope' => trim($scope),
				    'feedback_text' => trim($feedbackText),
				    'feedback' => trim($feedbackFlag),
				    'plan_status' => trim($planStatus),
				    'draft_flag' => trim($draftFlag),
				    'last_user' => trim($userID)
				))
			->where(array('id' => $id))
		    ;
		    
        $statement = $sql->prepareStatementForSqlObject($update);
        $statement->execute();
    }
    
    /**
     * Insert a new tuple into the plans table
     */
    public function insertPlan($metaFlag,$metaDescription,$year,$assessmentMethod,$population,$sampleSize,$assessmentDate,$cost,$analysisType,$administrator,$analysisMethod,$scope,$feedbackText,$feedbackFlag,$planStatus,$draftFlag,$userID)
    {
	// database timestamp format    
        //"1970-01-01 00:00:01";
        // gets the current timezone - date_default_timezone_get()
      
	// create the sytem timestamp
	$currentTimestamp = date("Y-m-d H:i:s", time());
      
	$sql = new Sql($this->adapter);
	$data = array('created_ts' => $currentTimestamp,
		      'submitted_ts' => null,
		      'modified_ts' => null,
		      'meta_flag' => $metaFlag,
		      'meta_description' => trim($metaDescription),
		      'year' => trim($year),
		      'assessment_method' => trim($assessmentMethod),
		      'population' => trim($population),
		      'sample_size' => trim($sampleSize),
		      'assessment_date' => trim($assessmentDate),
		      'cost' => trim($cost),
		      'analysis_type' => trim($analysisType),
		      'administrator' => trim($administrator),
		      'analysis_method' => trim($analysisMethod),
		      'scope' => trim($scope),
		      'feedback_text' => trim($feedbackText),
		      'feedback' => trim($feedbackFlag),
		      'plan_status' => trim($planStatus),
		      'draft_flag' => trim($draftFlag),
		      'last_user' => trim($userID));
		
	$insert = $sql->insert('plans');
	$insert->values($data);		    
		
	// create an automic database operation for the tuple insert and retreival of the auto-generated primary key		
	$connection = $this->adapter->getDriver()->getConnection();
	$connection->beginTransaction();

	// perform the insert
        $statement = $sql->prepareStatementForSqlObject($insert);
        $statement->execute();

	// get the primary key id
	$rowId = $this->adapter->getDriver()->getConnection()->getLastGeneratedValue();
		
	// finish the transaction		
	$connection->commit();
  
        return $rowId;
    }
    
    /**
     * Insert a tuple into the plan outcomes table
     */
    public function insertPlanOutcome($outcomeId, $planId)
    {
        $sql = new Sql($this->adapter);
	$data = array('outcome_id' => $outcomeId,
		      'plan_id' => $planId);

	$insert = $sql->insert('plan_outcomes');
	$insert->values($data);		    
    
        $statement = $sql->prepareStatementForSqlObject($insert);
        $statement->execute();
    }
    
    /**
     * Insert a tuple into the plan document table
     */
    public function insertPlanDocuments($planId, $fileName, $fileDescription)
    {
	/*
	 * split the file name into
	 *  1) File Name
	 *  2) File Ext
	 */
	$fileNameSplit = preg_split('/\./', $fileName, null, PREG_SPLIT_NO_EMPTY);
	
        $sql = new Sql($this->adapter);
	$data = array('plan_id' => $planId,
		      'file_name' => $fileNameSplit[0],
		      'file_ext' => $fileNameSplit[1],
		      'file_description' => $fileDescription,
		      );

	$insert = $sql->insert('plan_documents');
	$insert->values($data);		    
    
        $statement = $sql->prepareStatementForSqlObject($insert);
        $statement->execute();
    }
    
    /**
     * Get a plan document by plan id
     */
    public function getPlanDocumentsByPlanId($planId)
    {
/*
    select 
	file_name,
	file_ext,
	file_description
FROM `assessment`.`plans` p 
	inner join `assessment`.`plan_documents` pd
		on p.id = pd.plan_id
where p.id = 6026
;
*/
        $sql = new Sql($this->adapter);
        $select = $sql->select()
		      ->columns(array('file_name' => new Expression('plan_documents.file_name'),
				      'file_ext' => new Expression('plan_documents.file_ext'),
				      'file_description' => new Expression('plan_documents.file_description'),
				))
                      ->from('plans')
		      ->join('plan_documents', 'plan_documents.plan_id = plans.id')
		      ->where(array('plans.id' => $planId))
		   ;

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
	
        return $result;
    }
    
        /**
     * Get a plan by plan id
     */
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
    
        /**
     * Get all the outcoms by plan id
     */
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

        //create an array of entity objects to store the database results
	$entities = array();
        foreach ($resultSet as $row) {
            $entity = new Entity\Outcome("",$row['outcomeId'],$row['planId'],$row['outcomeText']);
            $entities[] = $entity;
        }
        return $entities;
    }
    
        /**
     * Get all the plans for the given deparment, program name, and year
     */
    public function getPlans($unitId, $names, $year)
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
                      ->where(array('units.id' => $unitId, 'programs.name' => $names, 'plans.year' => $year))
		      ->group (array('planId' => new Expression('plans.id')))
		   ;

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        return $result;
    }
    
    /**
     * Get all the outcomes for the given deparment, program name, and year
     */
    public function getOutcomes($unitId, $names, $year)
    {
/*
SELECT p.name, o.id, pl.id, o.outcome_text from assessment.units un
	inner join assessment.programs p
		on p.unit_id = un.id
    inner join assessment.outcomes o
		on o.program_id = p.id
	inner join assessment.plan_outcomes po
		on po.outcome_id = o.id
	inner join assessment.plans pl
		on pl.id = po.plan_id
where un.id = 'CSC'
	and p.name in ('BS Computer Science', 'BA Computer Science') 
	and pl.year = 2011
group by p.name, o.id, pl.id, o.outcome_text
;
*/	
        $sql = new Sql($this->adapter);
        $select = $sql->select()
                      ->columns(array('program' => new Expression('programs.name'),
                                      'outcomeId' => new Expression('outcomes.id'),
				      'planId' => new Expression('plans.id'),
				      'outcomeText' => new Expression('outcomes.outcome_text'),
				      ))
                      ->from('units')
		      ->join('programs', 'programs.unit_id = units.id')
		      ->join('outcomes', 'outcomes.program_id = programs.id')
		      ->join('plan_outcomes', 'plan_outcomes.outcome_id = outcomes.id')
		      ->join('plans', 'plans.id = plan_outcomes.plan_id')		      
		      ->where(array('units.id' => $unitId, 'programs.name' => $names, 'plans.year' => $year))
		      ->group (array('program' => new Expression('programs.name'),
                                     'outcomeId' => new Expression('outcomes.id'),
				     'planId' => new Expression('plans.id'),
				     'outcomeText' => new Expression('outcomes.outcome_text'),
				      ))
		   ;

        $statement = $sql->prepareStatementForSqlObject($select);
        $resultSet = $statement->execute();

        //create an array of entity objects to store the database results
	$entities = array();
        foreach ($resultSet as $row) {
            $entity = new Entity\Outcome($row['program'],$row['outcomeId'],$row['planId'],$row['outcomeText']);
            $entities[] = $entity;
        }
        return $entities;
    }
    
    /**
     * get all the unique outcomes by department, program, and year
     */
    public function getUniqueOutcomes($unitId, $names, $year)
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
                      ->columns(array('program' => new Expression('programs.name'),
                                      'outcomeId' => new Expression('outcomes.id'),
				      'outcomeText' => new Expression('outcomes.outcome_text'),
				      ))
                      ->from('units')
		      ->join('programs', 'programs.unit_id = units.id')
		      ->join('outcomes', 'outcomes.program_id = programs.id')
		      ->join('plan_outcomes', 'plan_outcomes.outcome_id = outcomes.id')
		      ->join('plans', 'plans.id = plan_outcomes.plan_id')		      
		      ->where(array('units.id' => $unitId, 'programs.name' => $names, 'plans.year' => $year))
		      ->group (array('program' => new Expression('programs.name'),
                                     'outcomeId' => new Expression('outcomes.id'),
				     'outcomeText' => new Expression('outcomes.outcome_text'),
				      ))
		   ;

        $statement = $sql->prepareStatementForSqlObject($select);
        $resultSet = $statement->execute();

        //create an array of entity objects to store the database results
	$entities = array();
        foreach ($resultSet as $row) {
            $entity = new Entity\Outcome($row['program'], $row['outcomeId'], 0,$row['outcomeText']);
            $entities[] = $entity;
        }
        return $entities;
    }
    
}