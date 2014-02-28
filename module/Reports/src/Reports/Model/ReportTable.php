<?php

namespace Reports\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;


class ReportTable extends AbstractTableGateway
{
  
    protected $table = 'reports';
    public $adapter;
    
    // Constructor
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }

    // Issues query to return the report associated with a plan id
    public function getReports($planId)
    {   
        $sql = new Sql($this->adapter);
        $select = $sql->select()
                      
                      ->from(array('r' => 'reports'), array('report_id' => 'id'))
                      ->join(array('p' => 'plans'),
                             'r.plan_id = p.id', array('assessment_method'))
                      ->join(array('po' => 'plan_outcomes'),
                             'p.id = po.plan_id', array())
                      ->join(array('o' => 'outcomes'),     
                            'po.outcome_id = o.id', array('outcome_text'))
                      ->where("r.plan_id = $planId");
                      
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        
        return $result;
    }
    
    // Takes report data as arguments and updates report data in database by report id
    public function updateReport($id, $population, $results, $conclusions, $actions){
            $sql = new Sql($this->adapter);
            $update = $sql->update()
                    ->table('reports')
                    ->set(array('population' => $population,
                                'results' => $results,
                                'conclusions' => $conclusions,
                                'actions' => $actions))
                    ->where("id = $id");
    
    
        $statement = $sql->prepareStatementForSqlObject($update);
        $statement->execute();
    }
    
    // Inserts a new report
    public function addReport($id, $population, $results, $conclusions, $actions){
        $sql = new Sql($this->adapter);
        $insert = $sql->insert('reports')
                    ->values(array('id' => $id,
                                   'population' => $population,
                                   'results' => $results,
                                   'conclusions' => $conclusions,
                                   'actions' => $actions));
        $statement = $sql->prepareStatementForSqlObject($insert);
        $statement->execute();
    }
    
    // Get's plan data associated with a program and year
    public function getPlans($program, $year)
    {   
        $sql = new Sql($this->adapter);
        $select = $sql->select()
                ->from(array('pr' => 'programs')) 
                ->join(array('o' => 'outcomes'),     
                    'pr.id = o.program_id')
                ->join(array('po' => 'plan_outcomes'),    
                    'po.outcome_id = o.id')
                ->join(array('pl' => 'plans'),     
                    'pl.id = po.plan_id')
                ->where("pr.id = $program")
                ->where("pl.year = $year");
  
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        
        return $result;
    }
}