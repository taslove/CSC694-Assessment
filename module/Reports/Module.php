<?php

namespace Reports;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter as DbAdapter;
use Reports\Model\ReportTable;
use Reports\Model\PlanTable;


class Module
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getServiceConfig()
    {
        
        return array(
                        
                'factories' =>  array(
                    'PlanTable' => function($sm) {
                        $dbAdapter = $sm->get('dbAdapter');
                        $table = new PlanTable($dbAdapter);
                        return $table;
                    },
                    'ReportTable' => function($sm) {
                        $dbAdapter = $sm->get('dbAdapter');
                        $table = new ReportTable($dbAdapter);
                        return $table;
                    },
                ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
}