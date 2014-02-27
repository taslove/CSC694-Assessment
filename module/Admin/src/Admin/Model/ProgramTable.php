<?php

namespace Admin\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class ProgramTable extends AbstractTableGateway
{
    public $adapter;

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->table = 'programs';
        $this->initialize();
    }

    public function fetchAll($paginated=false)
    {
         if($paginated) {
            // create a new Select object for the table album
            $select = new Select('programs');
            // create a new result set based on the Album entity
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new Program());
            // create a new pagination adapter object
            $paginatorAdapter = new DbSelect(
                // our configured select object
                $select,
                // the adapter to run it against
                $this->adapter,    
                #$this->tableGateway->getAdapter(),
                // the result set to hydrate
                $resultSetPrototype
            );
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;
        }
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