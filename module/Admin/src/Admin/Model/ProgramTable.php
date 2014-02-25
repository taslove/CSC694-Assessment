<?php

namespace Admin\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;

class ProgramTable extends AbstractTableGateway
{
    public $adapter;

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->table = 'programs';
        $this->initialize();
    }

    public function fetchAll()
    {
        $resultSet = $this->select();
        return $resultSet;
    }

    public function getProgram($id)
    {
        $id = (int) $id;
        $rowset = $this->select(array('id' => $id));
        
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveProgram(Program $program)
    {

        //if program doesn't exists
        if ($id == 0) {
            $this->insert($data);

        } else {
            if ($this->getProgram($id)) {
                $this->update($data, array('id' => $id));
  
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }

    public function deleteProgram($id)
    {
        $this->delete(array('id' => $id));
    }
}