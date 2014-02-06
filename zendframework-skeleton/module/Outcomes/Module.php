<?php

namespace Outcomes;

use Outcomes\Model\Enroll;
use Outcomes\Model\EnrollTable;
use Outcomes\Model\Student;
use Outcomes\Model\StudentTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

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
                        
               /* 'invokables' => array(
                     //register model table classes here....
                    'Outcomes\Model\StudentTable' => 'Outcomes\Model\StudentTable',
                    'Outcomes\Model\Student' => 'Outcomes\Model\Student',
                ),*/
                'factories' =>  array(
                    'Outcomes\Model\StudentTable' => function($sm) {
                        $dbAdapter = $sm->get('dbAdapter');
                        $table = new StudentTable($dbAdapter);
                        return $table;
                    },
                ),
       /*     'factories' => array(
                'Outcomes\Model\StudentTable' => function($sm) {
                    $tableGateway = $sm->get('StudentTableGateway');
                    $table = new StudentTable($tableGateway);
                    return $table;
                },
                'StudentTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Student());
                    return new TableGateway('Student', $dbAdapter, null, $resultSetPrototype);
                },
                'Outcomes\Model\EnrollTable' => function($sm) {
                    $tableGateway = $sm->get('EnrollTableGateway');
                    $table = new EnrollTable($tableGateway);
                    return $table;
                },
                'EnrollTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Enroll());
                    return new TableGateway('Enroll', $dbAdapter, null, $resultSetPrototype);
                },
            ),
       */
        );
        
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
}