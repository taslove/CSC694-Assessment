<?php

namespace Application\Authentication;
use Zend\Session\Container;

class AuthUser 
{
    public function Validate(){
        //This plugin validates that a user has logged in properly
        $namespace = new Container('user');
        if ($namespace->datatelID == null){
           return false;
        }
        else{
                return true;
        }       
    }
}