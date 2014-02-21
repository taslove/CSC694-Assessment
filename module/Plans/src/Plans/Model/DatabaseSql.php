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
    
    
    public function updatePlan($id,$assessmentMethod,$population,$sampleSize,$assessmentDate,$cost,$analysisType,$administrator,$analysisMethod,$scope,$feedback,$feedbackFlag,$planStatus,$draftFlag)
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
			->set(array('meta_flag' => trim($metaFlag),
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
				    'feedback' => trim($feedback),
				    'feedback_flag' => trim($feedbackFlag),
				    'plan_status' => trim($planStatus),
				    'draft_flag' => trim($draftFlag)
				))
			->where(array('id' => $id))
		    ;
		    
        $statement = $sql->prepareStatementForSqlObject($update);
        $statement->execute();
    }
    
    public function insertPlan($metaFlag,$metaDescription,$year,$assessmentMethod,$population,$sampleSize,$assessmentDate,$cost,$analysisType,$administrator,$analysisMethod,$scope,$feedback,$feedbackFlag,$planStatus,$draftFlag)
    {
	
//	var_dump($metaFlag);
//	var_dump($metaDescription);
//	var_dump($year);
//	var_dump($assessmentMethod);
//	exit;
	
        $sql = new Sql($this->adapter);
	$data = array('meta_flag' => $metaFlag,
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
		      'feedback' => trim($feedback),
		      'feedback_flag' => trim($feedbackFlag),
		      'plan_status' => trim($planStatus),
		      'draft_flag' => trim($draftFlag));
	
	$insert = $sql->insert('plans');
	$insert->values($data);		    
		    
        $statement = $sql->prepareStatementForSqlObject($insert);
        $statement->execute();
	
	
	// get the primary key of the last insert
        $select = $sql->select()
		      ->columns(array('maxId' => new Expression('MAX(id)')))
                      ->from('plans')
		   ;
		   
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
	
	// create and return  a single row
	$row = $result->current();   
        return $row;
    }
    
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