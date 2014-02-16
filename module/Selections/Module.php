<?php

namespace Selections;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class Module
{
       
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                array('Selections\Module' => __DIR__ . '/Module.php',
                      'Selections\View\Helper\SelectionsWidget' =>
                      __DIR__ . '/src/Selections/View/Helper/SelectionsWidget.php',
                ),
            ),
        );
    }

    public function getServiceConfig()
    {
        // set up each model table in factories
        return array(
                        
            'factories' =>  array(
                'Selections\Model\SelectionsTables' => function($sm) {
                    $dbAdapter = $sm->get('dbAdapter');
                    $table = new SelectionsTables($dbAdapter);
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