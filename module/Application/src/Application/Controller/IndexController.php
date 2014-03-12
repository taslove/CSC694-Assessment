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
    protected $tableResults;
    
    public function indexAction()
    {
        
        $namespace = new Container('user');
        $namespace->userID = '135';
        $namespace->role = 1;
        $namespace->userEmail = 'silahi@noctrl.edu';   
        $namespace->datatelID = 'silahi';
        
        //Render LoginForm
        $form = new LoginForm();
        return array('form' => $form);
    }
    
    public function authenticateAction()
    {
        //Get POST data
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
        
        //This is the result of the query to the authentication service
        $messages = $result->getMessages();
        
        
        //add code to check if student based on $messages2
        
        foreach ($messages2 as $message) {
            echo '<br>';
            var_dump($message);
        }
        
        
        //This checks the result of the authentication contained in $messages and
        //if successful, it stores the necessary data in the session container and moves on to the main page
        //otherwise it goes back to the login screen
        if (strpos($messages[3], 'successful') == TRUE) {
            
            //this next block is a hack.  the LDAP result on a successful authentication does not show if the
            //user is faculty, administration or student, but it does when the auth fails
            //I do a second auth with the password changed to make sure it is wrong and then check if it's a student
            //If it's a student, I treat it like a failed authentication
            $password2 = $password.$password;
            $adapter2 = new AuthAdapter($settings,
                                       $username,
                                       $password2);
            
            $result2 = $auth->authenticate($adapter2);
            
            //This is the result of the query to the authentication service
            $messages2 = $result2->getMessages();
            
            //if it was a student logging in, send them back otherwise continue
            if (strpos($messages2[3], 'stdnts') == FALSE)
                return $this->redirect()->toRoute('home');
            
            $results = $this->getAllTables()->getUserInformation($username);
            
            foreach ($results as $result) {
                $userID = $result['id'];
                $userEmail = $result['email'];
            }
            
            $results = $this->getAllTables()->getUserRole($userID);
            foreach ($results as $result)
                $userRole = $result['role'];
                
            $namespace = new Container('user');
            $namespace->userID = $userID;
            $namespace->role = $userRole;
            $namespace->userEmail = $userEmail;   
            $namespace->datatelID = $username;
            
            return $this->redirect()->toRoute('application');        
        }
        else {
            echo 'Authentication failed';
            return $this->redirect()->toRoute('home');
        }

        exit();        
    }
}
