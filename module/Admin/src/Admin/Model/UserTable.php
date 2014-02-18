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
                      #->join(array('ur'=>'user_roles'), 'ur.owner_id = users.id');
                      
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        
        $users = array();
        foreach($result as $row){
            $roles = $this->getRoles($row['id']);
            $row['role'] = $roles;
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
        $where->equalTo('owner_id',$id);
        $select->where($where);
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $roles = array();
        foreach($result as $row){
            $roles[$row['owner_id']]['role'] = $row['role'];
            $roles[$row['owner_id']]['id'] = $row['id'];
        }
        return $roles;
    }
<<<<<<< HEAD
=======
    
    public function deleteRoles($id)
    {
        $sql = new Sql($this->adapter);
        $delete = $sql->delete()
                      ->from('user_roles')
                      ->where('user_id = ?', $id);
        $deleteString = $sql->getSqlStringForSqlObject($delete);
        $this->adapter->query($deleteString, Adapter::QUERY_MODE_EXECUTE);
    }
    function addRoles($userID,$roles)
    {
       //add role(s)
       foreach($roles as $row => $value){
            $role = array(
                'user_id' => $userID,
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
>>>>>>> fb5ae052afdd1f904a9ad82c07c9a280fc9b6ba4


    public function getUser($id)
    {
        $id = (int) $id;
        $rowset = $this->select(array('id' => $id));
        
       /* $sql = new Sql($this->adapter);
        $select = $sql->select()
                      ->from($this->table)
                      ->join('user_roles', 'owner_id = users.id');
        $where = new Where();
        $where->equalTo('id',$id);
        $select->where($where);
                      
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();*/
        
        
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        $user = new User();
        $user->exchangeArray($row);
        return $user;
    }

    public function saveUser(User $user)
    {
        $data = array(
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'middle_init' => $user->middle_init,
        );
        
        $role = $user->role;

        $id = (int)$user->id;
<<<<<<< HEAD
        if ($id == 0) {
=======
        
        
        //if user doesn't exists
        if ($id == 0 OR empty($id)) {
            //insert user
>>>>>>> fb5ae052afdd1f904a9ad82c07c9a280fc9b6ba4
            $this->insert($data);
        } else {
            if ($this->getUser($id)) {
                $this->update($data, array('id' => $id));
                
                //update role
                $sql = new Sql($this->adapter);
                $update = $sql->update('user_roles')
                              ->set(array('role' => $role))
                              ->where('owner_id', $id);
  
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