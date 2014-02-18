<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Adapter;


class ProgramController extends AbstractActionController
{
    protected $tableResults;

   public function indexAction()
    {
        return new ViewModel(array(
            'programs' => $this->getProgramQueries()->fetchAll(),
        ));
    }
   public function addAction()
   {
 
   }
   public function editAction()
   {
       
   }
   public function deleteAction()
   {
    

   }
    
    public function getGenericQueries()
    {
        if (!$this->tableResults) {
            $this->tableResults = $this->getServiceLocator()
                                       ->get('Application\Model\AllTables');
                    
        }
        return $this->tableResults;
    }
   public function getProgramQueries()
    {
        if (!$this->tableResults) {
            $this->tableResults = $this->getServiceLocator()
                                       ->get('Admin\Model\ProgramTable');             
        }
        return $this->tableResults;
    }
}