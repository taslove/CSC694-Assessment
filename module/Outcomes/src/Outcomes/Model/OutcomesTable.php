<?php

namespace Outcomes\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;

use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;


// This class must be included in the factories array in
// this modules Module.php

// This class holds the database method calls.
// The class variable $table references a base table used in the
// queries.  This variable is required in the class but is not required
// to be used in all database calls.  For example, the from clause could
// explicitly reference a table (->from('student')).

class OutcomesTable extends AbstractTableGateway
{
  
    protected $table = 'outcomes';
    public $adapter;
    
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }

    public function getAllActiveOutcomesForProgram($programId)
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select()
                      ->from('outcomes')
                      ->where('program_id =' . $programId)
                      ->where('active_flag = 1');                
         
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        
        return $result;   
    }
     
    // inserts a new outcome in the database
    public function addOutcome($programId, $outcomeText, $userId)
    {
        $sql = new Sql($this->adapter);
        
        // store data from the outcome object that was passed in    
        $data = array('program_id' => $programId,
		      'outcome_text' => $outcomeText,
                      'active_flag' => 1,
                      'created_user' => $userId,
        );
            
        // excecute the SQL
        $insert = $sql->insert('outcomes');
        $insert->values($data);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $statement->execute();
    } 
    
    // swaps an existing outcome's active flag from 1 to 0
    public function deactivateOutcome($outcomeId, $userId)
    {
        $sql = new Sql($this->adapter);
        // switch the active flag from 0 to 1 then update only for the id passed in
	$update = $sql->update()
			->table('outcomes')
			->set(array('active_flag' => 0,
                                    'deactivated_user' => $userId,
			))
			->where(array('id' => $outcomeId
        ));                    
        $statement = $sql->prepareStatementForSqlObject($update);
        $statement->execute();
    }
    
    // creates a new outcome and deactivates an old one
    public function editOutcome($programId, $newOutcomeText, $deactivatedOutcomeId, $userId)
    {
        $sql = new Sql($this->adapter);       
        $this->addOutcome($programId, $newOutcomeText, $userId);
        $this->deactivateOutcome($deactivatedOutcomeId, $userId);       
    }
}