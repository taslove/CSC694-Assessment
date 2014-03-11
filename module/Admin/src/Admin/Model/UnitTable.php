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
use Zend\Debug\Debug;

class UnitTable extends AbstractTableGateway
{
    public $adapter;

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->table = 'units';
        $this->initialize();
    }

    public function fetchAll($paginated=false)
    {
         if($paginated) {
            // create a new Select object for the table album
            $select = new Select('units');
            // create a new result set based on the Album entity
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new Unit());
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

    public function getUnit($id)
    {
        $rowset = $this->select(array('id' => $id));
        
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        $unit = new Unit();
        $unit->exchangeArray($row);
        return $unit;
    }
    
    function addPriv($id,$user,$table)
    {
        $namespace = new Container('user');
        
            $data = array(
                'user_id' => $user,
                'unit_id' => $id,
                'created_user' => $namespace->userID,
                'created_ts' => date('Y-m-d g:i:s', time()),
                'active_flag' => 1
            );
            $sql = new Sql($this->adapter);
            $insert = $sql->insert($table);
            $insert->values($data);
            $insertString = $sql->getSqlStringForSqlObject($insert);
            $this->adapter->query($insertString, Adapter::QUERY_MODE_EXECUTE);

    }
    
    public function updatePriv($id,$user,$action,$table)
    {
        $namespace = new Container('user');
        switch($action){
            case 'disable':
                $data = array(
                        'active_flag' => 0,
                        'deactivated_ts' =>  date('Y-m-d g:i:s', time()),
                        'deactivated_user' =>  $namespace->userID
                    );
                break;
            
            case 'enable':
                $data = array(
                        'active_flag' => 1,
                    );
                break;
        }

        $sql = new Sql($this->adapter);
        $update = $sql->update($table)
                      ->set($data)
                       ->where(array('user_id = ?' => $user, 'unit_id = ?' => $id));
        $updateString = $sql->getSqlStringForSqlObject($update);
        $this->adapter->query($updateString, Adapter::QUERY_MODE_EXECUTE);
    }
    
    public function updatePrivs($id,$users,$table)
    {

        foreach($users as $key => $value)
        {     
   
            //get existing active liason ids
            $previousActivePrivs = $this->getUnitPrivs($id,$table,1);
            
            //get existing inactive liason ids
            $previousInactivePrivs = $this->getUnitPrivs($id,$table,0);
            
             //get all previous inactive liason ids
            $previousPrivs = $this->getUnitPrivs($id,$table,array(1,0));
            
            $newPrivs = array_diff(array($value),$previousPrivs);
            
            if(!empty($newPrivs)){
                foreach($newPrivs as $key => $value)
                {
                     $this->addPriv($id,$value,$table);
                }
            }
            
            //update role(s) that were re-enabled/disabled
            $disablePrivs = array_diff($previousActivePrivs, array($value));
            $reenabledPrivs = array_diff(array($value), $previousActivePrivs);


            if(!empty($disablePrivs)){
                foreach($disablePrivs as $key => $value){
                    $this->updatePriv($id,$value, 'disable',$table);
                }
            }
            if(!empty($reenabledPrivs)){
                foreach($reenabledPrivs as $key => $value){
                    $this->updatePriv($id,$value, 'enable',$table);
                }
            }
        } 
    }
    
    public function getUnitPrivs($id,$table,$active_flag)
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select($table)
                      ->columns(array('user_id'))
                      ->where(array('unit_id'=>$id, 'active_flag' =>$active_flag));                   
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $results = array();
        foreach($result as $key => $value){
            $results[] = $value['user_id'];
        }
        return $results;
    }
    
    
    public function saveUnit(Unit $unit)
    {
        $namespace = new Container('user');
        
        $data = array(
            'id' => $unit->id,
            'type' => $unit->type,
            'active_flag' => ($unit->active_flag)? $unit->active_flag: 0,
        );
              
        //deactivating an existing program
        if(!$unit->active_flag){
            $data['deactivated_ts'] =  date('Y-m-d g:i:s', time());
            $data['deactivated_user'] =  $namespace->userID;
        }
   
        //get the user id
        $id = $unit->id;
        
        $exists = $this->getUnit($id);
        
        //if program doesn't exists
        if (!$exists) {
            $data['created_ts'] =  date('Y-m-d g:i:s', time());
            $data['created_user'] = $namespace->userID;     
            
            
            $this->insert($data);
            
            
            $assessors[] = ($unit->assessor_1)? $unit->assessor_1:'';
            $assessors[] = ($unit->assessor_2)? $unit->assessor_2:'';
            $liaisons[] = ($unit->liaison_1)? $unit->liaison_1:'';
            $liaisons[] = ($unit->liaison_2)? $unit->liaison_2:'';
            
            if(!empty($assessors))
            {
                foreach($assessors as $key => $value)
                {
                    $this->addPriv($id,$value,'unit_privs');
                }
            }
            
            if(!empty($liaisons))
            {
                foreach($liaisons as $key => $value)
                {
                    $this->addPriv($id,$value,'liaison_privs');
                }
            }

        } else {
            if ($this->getUnit($id)) {
                
                $this->update($data, array('id' => $id));
                
                $assessors[] = ($unit->assessor_1)?$unit->assessor_1:'';
                $assessors[] = ($unit->assessor_2)?$unit->assessor_2:'';
                $liaisons[] = ($unit->liaison_1)?$unit->liaison_1:'';
                $liaisons[] = ($unit->liaison_2)?$unit->liaison_2:'';               
                
                $this->updatePrivs($id,$liaisons,'liaison_privs');
                $this->updatePrivs($id,$assessors,'unit_privs');
               
  
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }

    public function deleteUnit($id)
    {
        $this->delete(array('id' => $id));
    }
}