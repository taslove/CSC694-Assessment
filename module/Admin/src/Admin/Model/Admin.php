<?php

namespace Admin\Model;


//class for Generic Admin methods
class Admin 
{
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
    public function getRoleTerms(){
        $roles = array(
          '1' => 'Admin',
          '2' => 'Liason',
          '3' => 'Chair',
          '4' => 'Assessor',
          '5' => 'User',
        );
        return $roles;
    }
}