<?php
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Authentication\AuthUser;
use Zend\session\container;

class AdminController extends AbstractActionController
{
   protected $adminTable;

   public function onDispatch(\Zend\Mvc\MvcEvent $e) 
   {
        $validUser = new AuthUser();
        if (!$validUser->Validate()){
            return $this->redirect()->toRoute('application');
        }
        else{
            return parent::onDispatch( $e );
        }
        $namespace = new Container('user');
        
        return parent::onDispatch( $e );
   }
   
   public function indexAction()
   {
      
      
      
   }

}
