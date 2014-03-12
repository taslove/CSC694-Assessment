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
use Application\Form\ApplicationForm;
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
    public $message;
    
    public function indexAction()
    {
        $namespace = new Container('user');
        /*
        $namespace->userID = '40';
        $namespace->role = 1;
        $namespace->userEmail = 'akalelkar@noctrl.edu';   
        $namespace->datatelID = 'akalelkar';
        */
        
        //Render LoginForm        
        $form = new LoginForm();
        return array('form' => $form,
                     'message' => $namespace->message);
    }
    
    public function authenticateAction()
    {
        $namespace = new Container('user');
        //Get POST data
        $request = $this->getRequest();
        
        if ($request->isPost()) {
            $userName = $request->getPost('userName', null);
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
                                   $userName,
                                   $password);
        
        $result = $auth->authenticate($adapter);        
        //This is the result of the query to the authentication service
        $messages = $result->getMessages();
  
        
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
                                       $userName,
                                       $password2);
            
            $result2 = $auth->authenticate($adapter2);            
            //This is the result of the query to the authentication service
            $messages2 = $result2->getMessages();
            
            //if it was a student logging in, send them back otherwise continue
            if (strpos($messages2[3], 'stdnts') == FALSE) {
                $namespace->message = 'Assessment Portal accessible to Faculty and Administration only';
                return $this->redirect()->toRoute('home');
            }
            
            //Now we retrieve application specific information about the user using DB queries
            //located in Models\AllTables.php and store the info in the session namespace container
            $results = $this->getAllTables()->getUserInformation($userName);
            $namespace = new Container('user');

            
            foreach ($results as $result) {
                $namespace->userID = $result['id'];
                $namespace->userEmail = $result['email'];
            }
            
            $results = $this->getAllTables()->getUserRole($userID);
            foreach ($results as $result)
                $namespace->role = $result['role'];
                
            //$namespace->userID = $userID;
            //$namespace->role = $userRole;
            //$namespace->userEmail = $userEmail;   
            $namespace->datatelID = $userName;
            $namespace->message = NULL;
            
            return $this->redirect()->toRoute('application');        
        }
        else {            
            $namespace->message = 'Login failed';
            return $this->redirect()->toRoute('home');
        }   
    }
    
    public function getAllTables()
    {
        if (!$this->tableResults) {
            $sm = $this->getServiceLocator();
            $this->tableResults = $sm->get('Application\Model\AllTables');
        }
        return $this->tableResults;
    }
    
    public function logoutAction()
    {
        $namespace = new Container('user');
        $namespace->getManager()->getStorage()->clear();
        return $this->redirect()->toRoute('home');
    }
}
