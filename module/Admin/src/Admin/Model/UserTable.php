<?php

namespace Admin\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;

class UserTable extends AbstractTableGateway
{
    public $adapter;

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->table = 'users';
        $this->initialize();
    }

    public function fetchAll()
    {
        #$resultSet = $this->select();
        $sql = new Sql($this->adapter);
        $select = $sql->select()
                      ->from($this->table);
                      
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        
        $users = array();
        foreach($result as $row){
            $roles = $this->getRoles($row['id']);
            $row['user_roles'] = $roles;
            $user = new User();
            $user->exchangeArray($row);
            $users[] = $user;
            
        }
        return $users;
    }
    
    public function getRoles($id)
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select()
                      ->from('user_roles');
        
        $where = new Where();
        $where->equalTo('user_id',$id);
        $select->where($where);
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $roles = array();
        foreach($result as $row){
            $roles[$row['role']]['id'] = $row['id'];
            $roles[$row['role']]['term'] = $this->getRoleTerm($row['role']);
        }
        return $roles;
    }
    
    public function deleteRoles($id)
    {
        $sql = new Sql($this->adapter);
        $delete = $sql->delete()
                      ->from('user_roles')
                      ->where('owner_id', $id);
        $deleteString = $sql->getSqlStringForSqlObject($delete);
        $this->adapter->query($deleteString, Adapter::QUERY_MODE_EXECUTE);
    }
    function addRoles($userID,$roles)
    {
       //add role(s)
       foreach($roles as $row => $value){
            $role = array(
                'owner_id' => $userID,
                'role' => $value
            );
            $sql = new Sql($this->adapter);
            $insert = $sql->insert('user_roles');
            $insert->values($role);
            $insertString = $sql->getSqlStringForSqlObject($insert);
            $this->adapter->query($insertString, Adapter::QUERY_MODE_EXECUTE);
       }
    }
    
    public function getRoleTerm($id){
      if(!$id){
          return;
      }  
        $roles = array(
          '1' => 'Admin',
          '2' => 'Chair',
          '3' => 'User',
          '4' => 'Assessor',
          '5' => 'Committee',
        );
        return $roles[$id];
    }


    public function getUser($id)
    {
        $id = (int) $id;
        $rowset = $this->select(array('id' => $id));
        
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        $roles = $this->getRoles($row['id']);
        $row['user_roles'] = $roles;
        $user = new User();
        $user->exchangeArray($row);
        return $user;
    }

    public function saveUser(User $user)
    {
        //build the data array to add to users table
        $data = array(
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'middle_init' => $user->middle_init,
        );
        
        //just put users roles into a new array
        $roles = $user->user_roles;
        
        //get the user id
        $id = (int)$user->id;
        
        //if user doesn't exists
        if ($id == 0) {
            //insert user
            $this->insert($data);
            
            //get the new user id
            $id = $this->adapter->getDriver()->getLastGeneratedValue();

            //add role(s)
            $this->addRoles($id,$roles);
        } else {
            if ($this->getUser($id)) {
                
                //update user information
                $this->update($data, array('id' => $id));
                
                //delete old roles
                $this->deleteRoles($user->id);
                
                //add role(s)
                $this->addRoles($user->id,$roles);
  
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }

    public function deleteUser($id)
    {
        $this->delete(array('id' => $id));
    }
}