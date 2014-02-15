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
                      ->order('active DESC');
                     
         
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
                      ->where('unit_id = $unitId');
                     
         
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        
        return $result;   
    }
    
}