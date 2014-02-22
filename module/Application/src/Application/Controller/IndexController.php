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
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\Ldap as AuthAdapter;
use Zend\Config\Reader\Ini as ConfigReader;
use Zend\Config\Config;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream as LogWriter;
use Zend\Log\Filter\Priority as LogFilter;


class IndexController extends AbstractActionController
{
    
   /* public function indexAction()
    {        
        return new ViewModel();
    }
    */
    
    public function indexAction()
    {
       // $username = $this->getRequest()->getPost('username');
       // $password = $this->getRequest()->getPost('password');
        $username = "akalelkar";
        $password = "Frankie1";

        $auth = new AuthenticationService();

        $configReader = new ConfigReader();
        $configData = $configReader->fromFile('./ldap-config.ini');
        $config = new Config($configData, true);

        $log_path = $config->production->ldap->log_path;
        $options = $config->production->ldap->toArray();
        unset($options['log_path']);
        
        $adapter = new AuthAdapter($options,
                                   $username,
                                   $password);

        $result = $auth->authenticate($adapter);
        
        if ($log_path) {
            $messages = $result->getMessages();
        
            $logger = new Logger;
            $writer = new LogWriter($log_path);
        
            $logger->addWriter($writer);
        
            $filter = new LogFilter(Logger::DEBUG);
            $writer->addFilter($filter);
        
            foreach ($messages as $i => $message) {
                if ($i-- > 1) { // $messages[2] and up are log messages
                    $message = str_replace("\n", "\n  ", $message);
                    $logger->debug("Ldap: $i: $message");
                }
            }
        }
    }
}
