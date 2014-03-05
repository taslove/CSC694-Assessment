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


class IndexController extends AbstractActionController
{
    public function indexAction()
    {
    
        $form = new LoginForm();
        return array('form' => $form);
    }
    
    public function authenticateAction()
    {
        $form = new LoginForm();
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
        
        //this sets up the
        $auth = new AuthenticationService();
        $adapter = new AuthAdapter($options,
                                   $username,
                                   $password);
        
        $result = $auth->authenticate($adapter);
        
        $messages = $result->getMessages();
        var_dump($messages);
        exit();
    }
}


//var_dump($request);
        //exit();