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

        public function getAllActiveOutcomesForProgram($programId){
        $sql = new Sql($this->adapter);
        $select = $sql->select()
                      ->from('outcomes')
                      ->where('program_id ="' . $programId . '"')
                      ->where('active_flag = 1');
                     
                     
         
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        
        return $result;   
        }
     
    // used to retrieve an outcome by its ID (if it exists)
        public function getOutcome($id)
    {
        $id  = (int) $id;
        $rowset = $this->select(array(
            'id' => $id,
        ));
        $row = $rowset->current();

        if (!$row) {
            throw new \Exception("Could not find row $id");
        }

        return $row;
    }
    
    
    // called when done adding or editing an outcome
        public function saveOutcome(Outcomes $outcome)
    {
        $data = array(
            'id' => $outcome->oid,
            'program_id' => $outcome->programId,
            'outcome_text' => $outcome->outcomeText,
            'active_flag' => $outcome->activeFlag,
        );
        $id = (int)$outcome->oid;
        
        // if it doesn't have an id yet (meaning it's new)
        if ($id == false) {
            $this->insert($data);
        } else {
            if ($this->getOutcome($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }
    
    // called when done adding or editing an outcome
        public function addOutcome(Outcomes $outcome)
    {
        $sql = new Sql($this->adapter);
        
        // store data from the outcome object that was passed in    
        $data = array('id' => $outcome->oid,
		      'program_id' => $outcome->programId,
		      'outcome_text' => $outcome->outcomeText,
                      'active_flag' => $outcome->activeFlag,
            );
            
            $insert = $sql->insert('outcomes');
            $insert->values($data);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $statement->execute();
    }
    
    
    
        public function deactivateOutcome($outcomeId)
    {
        $sql = new Sql($this->adapter);
	$update = $sql->update()
			->table('outcomes')
			->set(array('active_flag' => 0,
			))
			->where(array('id' => $outcomeId
        ));
                        
        $statement = $sql->prepareStatementForSqlObject($update);
        $statement->execute();
    }
    
    
}