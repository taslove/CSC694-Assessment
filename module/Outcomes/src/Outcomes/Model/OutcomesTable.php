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
    
    /*
    public function getAllStudentEnroll()
    {   
        $sql = new Sql($this->adapter);
        $select = $sql->select()
                      ->from($this->table)
                      ->join('enroll', 'enroll.sid = student.sid');
                      
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        
        // dumping $result will not show any rows returned
        // you must iterate over $result to retrieve query results
        
        return $result;
    }
    */
    
    // returns the outcome table ordered active first
    public function getAllOutcomes()
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select()
                      ->from($this->table)
                    //  ->where('id = 1')
                      ->order('active_flag DESC');
                     
         
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        
        return $result;
    }
    
    public function getAllUnits(){
        
        $sql = new Sql($this->adapter);
        $select = $sql->select()
                      ->from('units');
                     
         
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        
        return $result;   
    }
    
        public function getAllProgramsForUnit($unitId){
        
        $sql = new Sql($this->adapter);
        $select = $sql->select()
                      ->from('programs')
                      ->where('unit_id ="' . $unitId . '"');
                     
         
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        
        return $result;   
    }
    
        public function getAllOutcomesForProgram($programId){
                  $sql = new Sql($this->adapter);
        $select = $sql->select()
                      ->from('outcomes')
                      ->where('program_id ="' . $programId . '"');
                     
         
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
    

        public function deleteOutcome($id)
    {
        $id = (int) $id;
        $this->delete(array('id' => $id));
    }
    
}