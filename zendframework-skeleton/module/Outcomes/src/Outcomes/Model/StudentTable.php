<?php

namespace Outcomes\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;


use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;

class StudentTable extends AbstractTableGateway
{
  
    protected $table = 'student';
    public $adapter;
    
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }
    
    public function fetchAll()
    {   
        $sql = new Sql($this->adapter);
        $select = $sql->select()
                      ->from($this->table)
                      ->join('enroll', 'enroll.sid = student.sid');
                      
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        
        //var_dump($select->getSqlString($this->adapter->getPlatform()));
        //exit();
        foreach($result as $r){
            var_dump($r);
            exit();
        }
        exit();
        return $result;
    }
}