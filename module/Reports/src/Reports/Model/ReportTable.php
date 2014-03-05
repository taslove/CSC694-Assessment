<?php

namespace Reports\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Expression;

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
    public function getReport($planId)
    {   
        $sql = new Sql($this->adapter);
        
        $select = $sql->select()
                      ->from(array('r' => 'reports'))
                      ->columns(array('id','population', 'results', 'conclusions', 'actions'))
                      ->join(array('p' => 'plans'),
                             'r.plan_id = p.id', array('year', 'meta_flag'))
                      ->join(array('po' => 'plan_outcomes'),
                             'p.id = po.plan_id', array())
                      ->join(array('o' => 'outcomes'),     
                            'po.outcome_id = o.id', array('text' => 'outcome_text'))
                      ->join(array('pr' => 'programs'),'o.program_id = pr.id',array('unit_id', 'name'))
                      ->where("p.id = $planId");
                      
        $select2 = $sql->select()
                       ->from(array('r' => 'reports'))
                       ->columns(array('id','population', 'results', 'conclusions', 'actions'))
                       ->join(array('p' => 'plans'),
                             'r.plan_id = p.id', array('year', 'meta_flag', 'text' => 'meta_description'))
                       ->join(array('mp' => 'meta_plans'),'p.id = mp.plan_id',array())
                        ->join(array('pr' => 'programs'),'mp.program_id = pr.id',array('unit_id', 'name'))

                        ->where("p.id = $planId");

        $select->combine($select2);       
                     
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
                    ->values(array('plan_id' => $id,
                                   'population' => $population,
                                   'results' => $results,
                                   'conclusions' => $conclusions,
                                   'actions' => $actions));
        $statement = $sql->prepareStatementForSqlObject($insert);
        $statement->execute();
    }
    
    // Just grabs reports for plan to see if one already exists
    // Returns count which should be 0
    public function reportExists($planId){
        $sql = new Sql($this->adapter);
        $select = $sql->select()
                    ->from(array('r' => 'reports'))
                    ->where(array('r.plan_id' => $planId));

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $count = $result->count();
        return $count;
    }
    
    public function getPlanForAdd($planId){
    $sql = new Sql($this->adapter);
        
        $select = $sql->select()
                      ->from(array('p' => 'plans'))
                      ->columns(array('id', 'year', 'meta_flag'))
                      ->join(array('po' => 'plan_outcomes'),
                             'p.id = po.plan_id', array())
                      ->join(array('o' => 'outcomes'),     
                            'po.outcome_id = o.id', array('text' => 'outcome_text'))
                      ->join(array('pr' => 'programs'),'o.program_id = pr.id',array('unit_id', 'name'))
                      ->where("p.id = $planId");
                      
        $select2 = $sql->select()
                       ->from(array('p' => 'plans'))
                        ->columns(array('id', 'year', 'meta_flag', 'text' => 'meta_description'))
                       ->join(array('mp' => 'meta_plans'),'p.id = mp.plan_id',array())
                        ->join(array('pr' => 'programs'),'mp.program_id = pr.id',array('unit_id', 'name'))

                        ->where("p.id = $planId");

        $select->combine($select2);       
                     
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        
        return $result;
    
    }
    
    public function getYears($programJson){
        $data = json_decode($programJson, true);
	$programs = $data['programs'];
     
        // Get all plan/outcome years
        $sql = new Sql($this->adapter);
        $select = $sql->select()
                ->from(array('pr' => 'programs'))
                ->columns(array())
                ->join(array('o' => 'outcomes'),     
                    'pr.id = o.program_id',
                    array())
                ->join(array('po' => 'plan_outcomes'),    
                    'po.outcome_id = o.id',
                    array())
                ->join(array('pl' => 'plans'),     
                    'pl.id = po.plan_id',
                    array('year' => new Expression('DISTINCT(year)')))
                ->where(array('pr.id' => $programs,
                              'pr.active_flag = 1'));
        
        // Get all plan/meta years
        $select2 = $sql->select()
                ->from(array('pl' => 'plans'))
                ->columns(array('year' => new Expression('DISTINCT(year)')))
                ->join(array('mp' => 'meta_plans'), 'mp.plan_id = pl.id',array())
                ->join(array('pr' => 'programs'), 'mp.program_id = pr.id', array())
                ->where(array('pr.id' => $programs,
                              'pr.active_flag = 1'));
        
        $select->combine($select2);
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        
        return $result;
        
    }
    
    // Get's plan data associated with a program and year
    public function getPlans($programJson)
    {
        

	$data = json_decode($programJson, true);
	$programs = $data['programs'];
        $year = $data['year'];
     
        // Get all plan/outcome data
        $sql = new Sql($this->adapter);
        $select = $sql->select()
                ->from(array('pr' => 'programs'))
                ->columns(array())
                ->join(array('o' => 'outcomes'),     
                    'pr.id = o.program_id',
                    array('text' => 'outcome_text'))
                ->join(array('po' => 'plan_outcomes'),    
                    'po.outcome_id = o.id',
                    array())
                ->join(array('pl' => 'plans'),     
                    'pl.id = po.plan_id',
                    array('id', 'meta_flag'))
                ->where(array('pl.year' => $year,
                              'pr.id' => $programs,
                              'pr.active_flag = 1'));
        
        // Get all plan/meta data
        $select2 = $sql->select()
                ->from(array('pl' => 'plans'))
                ->columns(array('text' => 'meta_description', 'id', 'meta_flag'))
                ->join(array('mp' => 'meta_plans'), 'mp.plan_id = pl.id',array())
                ->join(array('pr' => 'programs'), 'mp.program_id = pr.id', array())
                ->where(array('pl.year' => $year,
                              'pr.id' => $programs,
                              'pr.active_flag = 1'));
        
        $select->combine($select2);
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        
        return $result;
    }
}