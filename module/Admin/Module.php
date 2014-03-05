<?php

namespace Admin;

use Admin\Model\Admin;
use Admin\Model\AdminTable;
use Admin\Model\User;
use Admin\Model\Queries;
use Admin\Model\UserTable;
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
            'factories' => array(
                'Admin\Model\Queries' => function($sm) {
                    $dbAdapter = $sm->get('dbAdapter');
                    $table = new Queries($dbAdapter);
                    return $table;
                },
                'Admin\Model\UserTable' => function($sm) {
                    $dbAdapter = $sm->get('dbAdapter');
                    $table = new UserTable($dbAdapter);
                    return $table;
                },
                'UserTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new User());
                    return new TableGateway('users', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        ); 
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
}