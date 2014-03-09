<?php

namespace Admin\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;
use Admin\Model\Admin;

class UserTable extends AbstractTableGateway
{
    public $adapter;

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->table = 'users';
        $this->initialize();
    }

    /*
     * Returns all users in the user database
     */
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
    
    /*
     *  Get all roles by user id
     *  @id - the user id
     */
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
            $roles[$row['role']]['term'] = $this->Admin()->getRoleTerm($row['role']);
        }
        return $roles;
    }
    
    /*
     * deletes all roles a user has
     * @id - the user id
     */
    public function deleteRoles($id)
    {
        $sql = new Sql($this->adapter);
        $delete = $sql->delete('user_roles')
                       ->where(array('user_id = ?' => $id));
        $deleteString = $sql->getSqlStringForSqlObject($delete);
        $this->adapter->query($deleteString, Adapter::QUERY_MODE_EXECUTE);
    }
    
    /*
     *  Adds roles to a user
     * @userID - id to the user object
     * @roles - array of role ids
     */
    function addRoles($userID,$roles)
    {
       //add role(s)
       foreach($roles as $row => $value){
            $role = array(
                'user_id' => $userID,
                'role' => $value,
                'created_user' => 21
            );
            $sql = new Sql($this->adapter);
            $insert = $sql->insert('user_roles');
            $insert->values($role);
            $insertString = $sql->getSqlStringForSqlObject($insert);
            $this->adapter->query($insertString, Adapter::QUERY_MODE_EXECUTE);
       }
    }
    
    /*
     *  Updates Users Roles
     * @userID - id to the user object
     * @roles - array of role ids
     */
    function updateRoles($userID,$roles)
    {

    }
    
   

    /*
     * Get user by id
     * @returns null if no user is found or the user object
     */
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
    
    /*
     * Gets user by email address
     * @returns null if no user is found or the user object
     */
    public function getUserByEmail($email)
    {
        $rowset = $this->select(array('email' => $email));
        
        $row = $rowset->current();
        if (!$row) {
            return null;
        }
        $roles = $this->getRoles($row['id']);
        $row['user_roles'] = $roles;
        $user = new User();
        $user->exchangeArray($row);
        return $user;
    }

    /*
     * Saves a user
     */
    public function saveUser(User $user)
    {
        //build the data array to add to users table
        $data = array(
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'middle_init' => $user->middle_init,
            'email' => $user->email,
            'datatel_id' => 0
        );
        
        //just put users roles into a new array
        $roles = $user->user_roles;
        
        //get the user id
        $id = (int)$user->id;
        
        
        //if user doesn't exists
        if ($id == 0 OR empty($id)) {
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
                
                /*delete old roles
                $this->deleteRoles($user->id);
                
                //add role(s)
                $this->addRoles($user->id,$roles);*/
                
                //update and add roles
                $this->updateRoles($user->id,$roles);
  
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }

    /*
     * deletes a user
     */
    public function deleteUser($id)
    {

       //delete user roles first
       $this->deleteRoles($id);
       
       //delete the user
       $sql = new Sql($this->adapter);
       $delete = $sql->delete('users')
                       ->where(array('id = ?' => $id));
       $deleteString = $sql->getSqlStringForSqlObject($delete);
       $this->adapter->query($deleteString, Adapter::QUERY_MODE_EXECUTE);
    }
    
    public function Admin(){
        return new Admin();
    }
}