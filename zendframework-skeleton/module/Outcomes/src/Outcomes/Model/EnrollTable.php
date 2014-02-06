<?php

namespace Outcomes\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;

use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;

class EnrollTable extends AbstractTableGateway
{
    protected $table = 'enroll';

    public function __construct($adapter)
    {
        //parent::__construct('enroll', $adapter);
        $this->adapter = $adapter;
        $this->initialize();
    }

    public function fetchAll()
    {
        $sql = new SQL($this->adapter);
        $select = $sql->select());
        $select->from($this->table)
               ->join('student', student.sid = enroll.sid);
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
        // old code
        //$resultSet = $this->tableGateway->select();
        //return $resultSet;
    }

    public function getAlbum($id)
    {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

}