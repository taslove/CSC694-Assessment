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
use Zend\Ldap\Ldap;
use Zend\Session\Container;
use Application\Model\AllTables;


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
        $settings = $config->production->ldap->toArray();
        unset($settings['log_path']);
        
        //this sets up the adapter to talk to the LDAP server
        $auth = new AuthenticationService();
        $adapter = new AuthAdapter($settings,
                                   $username,
                                   $password);
        
        $result = $auth->authenticate($adapter);
        
        $messages = $result->getMessages();
        
        foreach ($messages as $message) {
            var_dump($message);
            echo '<br>';
        }
        
        $options = array(
            'host' => 'ldap.nccnet.noctrl.edu',
            'bindRequiresDn'    => true,
            'accountDomainName' => 'noctrl.edu',
            'baseDn'            => 'O=NCC',
        );
        
        $ldap = new Ldap($options);
        
        echo 'That worked';
        $ldap->bind();
        $userData = $ldap->getEntry('cn=akalelkar');
            var_dump($userData);
          
        
        
        $namespace = new Container('user');
        $namespace->usedID = 'Test ID';
        $namespace->role = 2;
        $namespace->userEmail = 'testID@foo.com';   
        $namespace->datatelID = 'NCC ID';
        
        $result = $this->getServiceConfig()->getUserInformation($username);


       // $this->ShowContainer();
        exit();        
    }
    
    public function ShowContainer()
    {
        
        $namespace = new Container('user');
        foreach ($namespace as $content)
            var_dump($content);
        
    }
    
    
}
