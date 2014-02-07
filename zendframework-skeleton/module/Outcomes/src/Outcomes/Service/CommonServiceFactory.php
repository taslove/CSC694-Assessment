<?php
namespace Outcomes\Service;
 
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
 
class CommonServiceFactory implements FactoryInterface
{
    protected $controller;
     
    public function createService(ServiceLocatorInterface $services)
    {var_dump("here");
    exit();
        $serviceLocator = $services->getServiceLocator();
        $dbAdapter      = $serviceLocator->get('Zend\Db\Adapter\Adapter');
         
        $controller = new $this->controller;
        $controller->setDbAdapter($dbAdapter);
         
        return $controller;
    }
     
    //setter controller
    public function setController($controller)
    {
        $this->controller = $controller;
    }
}