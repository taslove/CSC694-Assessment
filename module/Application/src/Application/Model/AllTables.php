<?php

namespace Application\Model;

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
  
}