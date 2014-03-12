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
use Zend\session\container;

class ProgramTable extends AbstractTableGateway {

    public $adapter;

    public function __construct(Adapter $adapter) {
        $this->adapter = $adapter;
        $this->table = 'programs';
        $this->initialize();
    }

    /*
     * Get all Programs and return in paginator or resultset
     */
    public function fetchAll($paginated = false) {
        if ($paginated) {
            // create a new Select object for the table album
            $select = new Select();
            $select->from('programs')
                    ->columns(array('prog_id' => 'id','unit_id','name','created_ts','active_flag'))
                    ->join(array('u' => 'users'), 'u.id = created_user');
            // create a new result set based on the Program entity
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

    /*
     * Get program by id
     */
    public function getProgram($id) {
        $id = (int) $id;
        $rowset = $this->select(array('id' => $id));

        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        $program = new Program();
        $program->exchangeArray($row);
        return $program;
    }

    /*
     * Save a Program
     */
    public function saveProgram(Program $program) {
        $namespace = new Container('user');

        //build the save data array 
        $data = array(
            'unit_id' => $program->unit_id,
            'name' => $program->name,
            #'active_flag' => ($program->active_flag) ? $program->active_flag : 0, //removed active checkbox from form
            'active_flag' => 1,
        );

        //deactivating an existing program
        if (!$program->active_flag) {
            $data['deactivated_ts'] = date('Y-m-d g:i:s', time());
            $data['deactivated_user'] = $namespace->userID;
        }

        //get the program id
        $id = (int) $program->id;

        //if program doesn't exists
        if ($id == 0) {
            $data['created_ts'] = date('Y-m-d g:i:s', time());
            $data['created_user'] = $namespace->userID;
            $this->insert($data);
        } else {
            if ($this->getProgram($id)) {
                $this->update($data, array('id' => $id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }

    /*
     * Delete Program
     */
    public function deleteProgram($id) {
        $this->delete(array('id' => $id));
    }

}