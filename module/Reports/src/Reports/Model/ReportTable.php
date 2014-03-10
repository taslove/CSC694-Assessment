<?php

namespace Reports\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Expression;

class ReportTable extends AbstractTableGateway
{
    // Our DB adapter
    public $adapter;
    protected $table = 'reports';
    
    // Constructor
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }

    // Issues query to return the report associated with a plan id
    public function getReport($planId)
    {   
        $sql = new Sql($this->adapter);
        
        // This returns all active reports with outcomes
        $select = $sql->select()
                        ->from(array('r' => 'reports'))
                        ->columns(array('id','population', 'results', 'conclusions', 'actions', 'draft_flag', 'feedback', 'feedback_text'))
                        ->join(array('p' => 'plans'),
                               'r.plan_id = p.id', array('year', 'meta_flag'))
                        ->join(array('po' => 'plan_outcomes'),
                               'p.id = po.plan_id', array())
                        ->join(array('o' => 'outcomes'),     
                              'po.outcome_id = o.id', array('text' => 'outcome_text'))
                        ->join(array('pr' => 'programs'),'o.program_id = pr.id',array('unit_id', 'name'))
                        ->where(array("p.id = $planId", "r.deactivated_user IS NULL"));
        
        // This returns all active reports with assessment   
        $select2 = $sql->select()
                        ->from(array('r' => 'reports'))
                        ->columns(array('id','population', 'results', 'conclusions', 'actions', 'draft_flag', 'feedback', 'feedback_text'))
                        ->join(array('p' => 'plans'),
                              'r.plan_id = p.id', array('year', 'meta_flag', 'text' => 'meta_description'))
                        ->join(array('pp' => 'plan_programs'),'p.id = pp.plan_id',array())
                        ->join(array('pr' => 'programs'),'pp.program_id = pr.id',array('unit_id', 'name'))
                        ->where(array("p.id = $planId", "r.deactivated_user IS NULL"));
        
        // Combine the two queries
        $select->combine($select2);       
                    
        // Execute and return results 
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }
    
    // Takes all report data from form needed for updating a report
    // All arguments match column names in report table except
    // $status - this is 0 for submitted, 1 for draft, 2 for delete the thing
    // $user - user ID to be inserted where appropriate
    public function updateReport($id, $population, $results, $conclusions, $actions, $status, $user, $feedbackText, $feedback){
        
        // Get time for timestamps
        $now = date("Y-m-d H:i:s", time());

        // Add values to array that we use no matter what status
        $values = array('population' => $population,
                            'results' => $results,
                            'conclusions' => $conclusions,
                            'actions' => $actions,
                            'modified_user' => $user,
                            'modified_ts' => $now,
                            'feedback_text' => $feedbackText,
                            'feedback' => $feedback);
        
        // If status is 2, we are deactivating the report
        // So add deactivated user and ts, but don't change draft_flag
        if($status == 2){
            $values = array_merge(array('deactivated_ts' => $now, 'deactivated_user' => $user), $values);
        
        // Otherwise if we aren't deleting, we just update the draft flag with status
        // which will be either 0 or 1
        }else{
            $values = array_merge(array('draft_flag' => $status), $values);
        }
        
        // Formulate update
        $sql = new Sql($this->adapter);
        $update = $sql->update()
                ->table('reports')
                ->set($values)
                ->where("id = $id");

        // Execute this bad boy
        $statement = $sql->prepareStatementForSqlObject($update);
        $statement->execute();
    }
    
    // Inserts a new report into the DB
    // All arguments match column names except
    // $id - plan id report is associated to
    // $status - here is draft_flag, 0 or 1
    // $user - user Id of user adding report
    public function addReport($id, $population, $results, $conclusions, $actions, $status, $user){
        
        // Grab date for timestamps
        $now = date("Y-m-d H:i:s", time());

        $sql = new Sql($this->adapter);
        
        // Add values to array that go in regardless of status
        $values = array('plan_id' => $id,
                                'population' => $population,
                                'results' => $results,
                                'conclusions' => $conclusions,
                                'actions' => $actions,
                                'draft_flag' => $status,
                                'created_ts' => $now,
                                'created_user' => $user,
                                'modified_user' => $user,
                                'modified_ts' => $now,
                                'feedback' => '1');
        
        // If status is 0, this is not a draft and merge submitted user and ts to values
        if($status == 0){
            $values = array_merge(array('submitted_ts' => $now, 'submitted_user' => $user), $values);
        }
        
        // Create insert statement
        $insert = $sql->insert('reports')
                    ->values($values);
        
        // Execute insert
        $statement = $sql->prepareStatementForSqlObject($insert);
        $statement->execute();
        return $this->adapter->getDriver()->getLastGeneratedValue();
    }
    
    // Just grabs report count for plan to see if one already exists
    // Returns count
    public function reportExists($planId){
        $sql = new Sql($this->adapter);
        
        // Grab active reports for selected plan ID
        $select = $sql->select()
                    ->from(array('r' => 'reports'))
                    ->where(array('r.plan_id' => $planId, "r.deactivated_user IS NULL"));

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $count = $result->count();
        
        // Returns just the count, not the results
        return $count;
    }
    
    // This grabs plan data we're adding a report to
    // Different than getPlans in that it only returns one plan with
    // selected plan id
    public function getPlanForAdd($planId){
        $sql = new Sql($this->adapter);
        
        // This grabs plan data if it has outcomes
        $select = $sql->select()
                      ->from(array('p' => 'plans'))
                      ->columns(array('id', 'year', 'meta_flag'))
                      ->join(array('po' => 'plan_outcomes'),
                             'p.id = po.plan_id', array())
                      ->join(array('o' => 'outcomes'),     
                            'po.outcome_id = o.id', array('text' => 'outcome_text'))
                      ->join(array('pr' => 'programs'),'o.program_id = pr.id',array('unit_id', 'name'))
                      ->where("p.id = $planId");
             
        // This grabs plan data if it has assessment         
        $select2 = $sql->select()
                       ->from(array('p' => 'plans'))
                        ->columns(array('id', 'year', 'meta_flag', 'text' => 'meta_description'))
                       ->join(array('pp' => 'plan_programs'),'p.id = pp.plan_id',array())
                        ->join(array('pr' => 'programs'),'pp.program_id = pr.id',array('unit_id', 'name'))

                        ->where("p.id = $planId");
        
        // Combine queries so only have to query onece
        $select->combine($select2);       
              
        // Create statemnt and execute       
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        
        return $result;
    }
    
    // Get's all plans that match left nav search criteria
    public function getPlans($programJson)
    {
        // Get data from json
        $data = json_decode($programJson, true);
	$programs = $data['programs'];
        $year = $data['year'];
     
        // Get all plan/outcome data
        $sql = new Sql($this->adapter);
        $select = $sql->select()
                ->from(array('pr' => 'programs'))
                ->columns(array())
                ->join(array('o' => 'outcomes'),     
                        'pr.id = o.program_id',
                        array('text' => 'outcome_text'))
                ->join(array('po' => 'plan_outcomes'),    
                        'po.outcome_id = o.id',
                        array())
                ->join(array('pl' => 'plans'),     
                        'pl.id = po.plan_id',
                        array('id', 'meta_flag'))
                ->where(array('pl.year' => $year,
                        'pr.id' => $programs,
                        'pr.active_flag = 1'));
        
        // Get all plan/meta data
        $select2 = $sql->select()
                ->from(array('pl' => 'plans'))
                ->columns(array('text' => 'meta_description', 'id', 'meta_flag'))
                ->join(array('pp' => 'plan_programs'), 'pp.plan_id = pl.id',array())
                ->join(array('pr' => 'programs'), 'pp.program_id = pr.id', array())
                ->where(array('pl.year' => $year,
                        'pr.id' => $programs,
                        'pr.active_flag = 1'));
        
        // Combine queries to submit once
        $select->combine($select2);
        
        // Create statment and execute
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        
        return $result;
    }
    
    // Get all document data for selected report id
    public function getDocuments($id){
        $sql = new Sql($this->adapter);
        
        // Grab active reports for selected plan ID
        $select = $sql->select()
                    ->from(array('rd' => 'report_documents'))
                    ->columns(array('file_name', 'file_description', 'file_ext', 'id'))
                    ->where(array('rd.report_id' => $id));

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        
        return $result;
    }
    
    // Get document data for specific document id
    public function getDocument($id){
        $sql = new Sql($this->adapter);
        
        $select = $sql->select()
                    ->from(array('rd' => 'report_documents'))
                    ->where(array('rd.id' => $id));

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        
        return $result;
    }
    
    // Save all files
    // $files - Array of files
    // $id - The report id these files belongs to
    // $user who put it there
    public function saveFiles($files, $id, $user){       
        foreach($files as $f){
            $sql = new Sql($this->adapter);
            $insert = $sql->insert('report_documents')
                        ->values(array('file_name' => $f['name'],
                                       'report_id' => $id,
                                       'created_user' => $user,
                                       'file_ext' => $f['ext'],
                                       'file_document' => $f['content'],
                                       'file_description' => $f['description']));
    
            
            $statement = $sql->prepareStatementForSqlObject($insert);
            $statement->execute();
        }
    }
    
    // Delete 1 or more files
    // $ids - array of report document ids for deletion
    public function deleteFiles($ids){
                
            $sql = new Sql($this->adapter);
            $delete = $sql->delete('report_documents')
                ->where(array('id' => $ids));
            $statement = $sql->prepareStatementForSqlObject($delete);
            $statement->execute();
        
    }
}