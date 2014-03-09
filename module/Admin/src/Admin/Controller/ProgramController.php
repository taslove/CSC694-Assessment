<?php
namespace Admin\Controller;

use Admin\Model\Program;
use Admin\Form\ProgramForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Adapter;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\DbSelect;
use Application\Authentication\AuthUser;
use Zend\session\container;

class ProgramController extends AbstractActionController
{
   protected $tableResults;

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
        $paginator = $this->getProgramQueries()->fetchAll(true);
        // set the current page to what has been passed in query string, or to 1 if none set
        $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
        // set the number of items per page to 10
        $paginator->setItemCountPerPage(10);
        
        $form = new ProgramForm();
        $form->get('submit')->setValue('Add');

        return new ViewModel(array(
            'paginator' => $paginator,
            'form' => $form
        ));
    }
    
   public function addAction()
   {
        $form = new ProgramForm();
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $program = new Program();
            $form->setInputFilter($program->getInputFilter());
            $form->setData($request->getPost());

            if (!$form->isValid()) {
                print_r($form->getMessages());
            }
            if ($form->isValid()) {
                $program->exchangeArray($form->getData());
                
                //TODO: something to check it email exists
                
                //save the program
                $this->getProgramQueries()->saveProgram($program);

                // Redirect to list of users
                return $this->redirect()->toRoute('program');
            }
        }
        return array('form' => $form);  
   }
   public function editAction()
   {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('program', array(
                'action' => 'add'
            ));
        }
        $program = $this->getProgramQueries()->getProgram($id);

        $form = new ProgramForm();
        $form->bind($program);
        $form->get('submit')->setAttribute('value', 'Save');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($program->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getProgramQueries()->saveProgram($form->getData());

                // Redirect to list of users
                return $this->redirect()->toRoute('program');
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
            return $this->redirect()->toRoute('program');
        }       
        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getProgramQueries()->deleteProgram($id);
            }

            // Redirect to list of users
            return $this->redirect()->toRoute('user');
            
        }else{
            $this->getProgramQueries()->deleteProgram($id);
            return $this->redirect()->toRoute('program');
        }
   }
    

   public function getProgramQueries()
    {
        if (!$this->tableResults) {
            $this->tableResults = $this->getServiceLocator()
                                       ->get('Admin\Model\ProgramTable');             
        }
        return $this->tableResults;
    }    
}
