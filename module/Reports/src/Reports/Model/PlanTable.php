<?php

namespace Reports\Model;

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

class PlanTable extends AbstractTableGateway
{  
    public $adapter;
    
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }

    public function getPlans()
    {

        $sql = new Sql($this->adapter);
        $select = $sql->select()
                      ->from('plans');
                      
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        return $result;
    }
    
    public function getPlans1()
    {   
        $sql = new Sql($this->adapter);
        $select = $db->select()
             ->from(array('p' => 'plans'), 'id')
             ->join(array('po' => 'plan_outcomes'),
                    'po.plan_id = p.id',
                    array())
             ->join(array('0' => 'outcomes'),
                    'o.id = po.outcome_id',
                    'outcome_text')
             ->where('p.program_id = 16')
             ->where('p.year = 2010')
             ->where('o.active = 1')
             ->order('p.id ASC');
                     
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        
        return $result;
    }
}