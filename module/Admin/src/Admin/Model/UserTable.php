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
        $where->equalTo('owner_id',$id);
        $select->where($where);
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $roles = array();
        foreach($result as $row){
            $roles[$row['id']]['id'] = $row['role'];
            $roles[$row['id']]['term'] = $this->getRoleTerm($row['role']);
        }
        return $roles;
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
        $row['rolesdb'] = $roles;
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
        
        $role = $user->user_roles;
        
        $id = (int)$user->id;
        if ($id == 0) {
            $this->insert($data);
            $id = $this->adapter->getDriver()->getLastGeneratedValue();

            //add role(s)
            foreach($role as $row => $value){
                $role = array(
                    'owner_id' => $id,
                    'role' => $value
                );
                $sql = new Sql($this->adapter);
                $insert = $sql->insert('user_roles');
                $insert->values($role);
                $insertString = $sql->getSqlStringForSqlObject($insert);
                $this->adapter->query($insertString, Adapter::QUERY_MODE_EXECUTE);
            }
        } else {
            if ($this->getUser($id)) {
                $this->update($data, array('id' => $id));
                
                //update role(s)
                foreach($role as $row => $value){
                    $sql = new Sql($this->adapter);
                    $update = $sql->update('user_roles')
                                  ->set(array('role' => $value))
                                  ->where('id', $role_id);
                    $updateString = $sql->getSqlStringForSqlObject($insert);
                    $this->adapter->query($updateString, Adapter::QUERY_MODE_EXECUTE);
                }
  
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