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
}