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


class IndexController extends AbstractActionController
{
    public function indexAction()
    {
      
        $form = new LoginForm();
        
        var_dump($form);
        exit();
        return new ViewModel();
    }
    
    public function authenticateAction()
    {
        
    }
}
