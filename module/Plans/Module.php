<?php

namespace Plans;

use Plans\Model\DatabaseSql;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Db\Adapter\Adapter as DbAdapter;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }
    
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
        // set up each model table in factories
        return array(
            'factories' =>  array(
                'dbAdapter' => function($sm) {
                    $config = $sm->get('config');
                    $config = $config['db'];
                    $dbAdapter = new DbAdapter($config);
                    return $dbAdapter;
                },
                'Plans\Model\DatabaseSql' => function($sm) {
                    $dbAdapter = $sm->get('dbAdapter');
                    $tableMock = new DatabaseSql($dbAdapter);
                    return $tableMock;                    
                },                    
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
}