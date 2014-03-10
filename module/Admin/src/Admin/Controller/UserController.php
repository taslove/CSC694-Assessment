<?php

namespace Admin\Controller;

use Admin\Model\User;
use Admin\Entity\UserObj;
use Admin\Form\UserForm;
use Admin\Form\NewUserForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Adapter;
use Zend\Paginator\Adapter\ArrayAdapter;
use Zend\Paginator\Paginator;
use Application\Authentication\AuthUser;
use Zend\session\container;


class UserController extends AbstractActionController
{
   protected $tableResults;
   protected $generictableResults;

   public function onDispatch(\Zend\Mvc\MvcEvent $e) 
   {
         /* $validUser = new AuthUser();
        if (!$validUser->Validate()){
            return $this->redirect()->toRoute('application');
        }
        else{
            return parent::onDispatch( $e );
        }*/
        $namespace = new Container('user');
        $namespace->userID = 21;
        $namespace->userEmail = 'testID@foo.com';
        $namespace->role = 2;
        $namespace->datatelID = 11123;
        
       
        
        return parent::onDispatch( $e );
   }
   
   public function indexAction()
    { 
        $page = $this->params()->fromRoute('page') ? (int) $this->params()->fromRoute('page') : 1;

        $users = $this->getUserQueries()->fetchAll();
        $itemsPerPage = 10;

        $paginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($users));
        $paginator->setCurrentPageNumber($page)
                ->setItemCountPerPage($itemsPerPage)
                ->setPageRange(7);

        $args['roles'] = $this->getGenericQueries()->getRoleTerms();
        $form = new UserForm(null,$args);
        $form->get('submit')->setValue('Add');

        return new ViewModel(array(
                    'page' => $page,
                    'paginator' => $paginator,
                    'form' => $form
                ));
    }
    
   public function newuserAction()
   {
     $form = new NewUserForm();
     $user = new UserObj();
     $form->bind($user);

     if ($this->request->isPost()) {
         $form->setData($this->request->getPost());

         if ($form->isValid()) {
             var_dump($form);
         }
     }

     return array(
           'form' => $form
     );
   }
    
   public function addAction()
   {
        $args['roles'] = $this->getGenericQueries()->getRoleTerms();
        $form = new UserForm(null,$args);
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $user = new User();
            $form->setInputFilter($user->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $user->exchangeArray($form->getData());
                
                //TODO: something to check it email exists
                
                //save the user
                $this->getUserQueries()->saveUser($user);

                // Redirect to list of users
                return $this->redirect()->toRoute('user');
            }
        }
        return array('form' => $form);  
   }
   public function editAction()
   {
       $id = (int) $this->params()->fromRoute('id');
        if (!$id) {
            return $this->redirect()->toRoute('user', array(
                'action' => 'add'
            ));
        }
        $user = $this->getUserQueries()->getUser($id);



        $user->dbroles = $user->user_roles;

        foreach($user->user_roles as $role => $value){
            $user->user_roles[] = $role;
        }
        $user->units =  'MTH';
        
        $args['roles'] = $this->getGenericQueries()->getRoleTerms();
        $args['units'] = $this->getGenericQueries()->getUnits();
        $args['action'] = 'edit';
        $args['user_id'] = $id;
        $form = new UserForm(null,$args);
        $form->bind($user);
        $form->get('submit')->setAttribute('value', 'Save');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($user->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getUserQueries()->saveUser($form->getData());

                // Redirect to list of users
                return $this->redirect()->toRoute('user');
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
        );
   }
   public function deleteAction()
   {       
       $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('user');
        }       
        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getUserQueries()->deleteUser($id);
            }

            // Redirect to list of users
            return $this->redirect()->toRoute('user');
            
        }else{
            $this->getUserQueries()->deleteUser($id);
            return $this->redirect()->toRoute('user');
        }

   }
    public function getGenericQueries()
    {
        if (!$this->generictableResults) {
            $this->generictableResults = $this->getServiceLocator()
                                       ->get('Admin\Model\Generic');             
        }
        return $this->generictableResults;
    }
   public function getUserQueries()
    {
        if (!$this->tableResults) {
            $this->tableResults = $this->getServiceLocator()
                                       ->get('Admin\Model\UserTable');             
        }
        return $this->tableResults;
    }
}
