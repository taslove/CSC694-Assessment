<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Review\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Adapter;

class ReviewController extends AbstractActionController
{
    protected $tableResults;
    
    public function indexAction()
    {
        // get units
        $results = $this->getGenericQueries()->getUnits();
        // iterate over database results forming a php array
        foreach ($results as $result) : 
            $unitarray[] = $result;
        endforeach;
        // pass array to view
        return new ViewModel(array(
            'units' => $unitarray,
        ));
    }
    
    public function getAction()
    {
        var_dump("hello");
        exit();

        $unitChosen = (int) $this->params()->fromRoute('id', 0);
        $data = json_encode($this->getGenericQueries()->getProgramsByUnitId($unitChosen));
        
        return $data;
    }
    
    public function getGenericQueries()
    {
        if (!$this->tableResults) {
            $this->tableResults = $this->getServiceLocator()
                                       ->get('Application\Model\AllTables');
                    
        }
        return $this->tableResults;
    }
    public function getReviewQueries()
    {
        if (!$this->tableResults) {
            $this->tableResults = $this->getServiceLocator()
                                       ->get('Review\Model\ReviewTables');
                    
        }
        return $this->tableResults;
    }
}
