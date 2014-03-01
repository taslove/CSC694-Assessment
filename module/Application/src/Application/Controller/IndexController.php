<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Form\LoginForm;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\Ldap as AuthAdapter;
use Zend\Config\Reader\Ini as ConfigReader;
use Zend\Config\Config;
use Zend\Ldap;


class IndexController extends AbstractActionController
{
    public function indexAction()
    {
    
        $form = new LoginForm();
        return array('form' => $form);
    }
    
    public function authenticateAction()
    {
        $request = $this->getRequest();
        
        if ($request->isPost()) {
            $username = $request->getPost('userName', null);
            $password = $request->getPost('password', null);
        }
     
        //this code reads the configuration file for the LDAP server
        $configReader = new ConfigReader();
        $configData = $configReader->fromFile('ldap-config.ini');
        $config = new Config($configData, true);        
        $options = $config->production->ldap->toArray();
        unset($options['log_path']);
        
        //this sets up the adapter to talk to the LDAP server
        $auth = new AuthenticationService();
        $adapter = new AuthAdapter($options,
                                   $username,
                                   $password);
        
        $result = $auth->authenticate($adapter);
        
        $messages = $result->getMessages();
        
        foreach ($messages as $message) {
            var_dump($message);
            echo '<br>';
        }
        
        //$ldap = new Zend\Ldap\Ldap($options);
        //var_dump($ldap);
        
        //$schema = $auth->getSchema();
        
        
        
        exit();        
    }
}
