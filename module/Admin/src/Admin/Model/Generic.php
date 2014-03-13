<?php

namespace Admin\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;
use Admin\Model\Admin;
use Zend\session\container;

class Generic extends AbstractTableGateway
{
    public $adapter;

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        #$this->table = '';
        #$this->initialize();
    }
    public function getUnits()
    {
      $sql = new Sql($this->adapter);
      $select = $sql->select()
                    ->from('units')
                    ->columns(array('id' => 'id'));
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $results = array();
        if($result){
            foreach($result as $row => $value){
                $results[$value['id']]= $value['id'];
            }
        }
        return $results;
    }
    /*
     * Assigns a role id to a term
     */
    public function getRoleTerm($id){
      if(!$id){
          return;
      }  
        $roles = array(
          '1' => 'Admin',
          '2' => 'Liason',
          '3' => 'Chair',
          '4' => 'Assessor',
          '5' => 'User',
        );
        return $roles[$id];
    }
    /*
     * Assigns a role id to a term
     */
    public function getRoleTerms($admin = true){
        $roles = array(
          '1' => 'Admin',
          '2' => 'Liason',
          '3' => 'Chair',
          '4' => 'Assessor',
          '5' => 'User',
        );
        if(!$admin)
        {
            unset($roles[1]);
        }
        
        return $roles;
    }

    public function Admin(){
        return new Admin($adapter);
    }
}