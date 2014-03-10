<?php
namespace Admin\Form;

use Admin\Entity\UserObj;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;
use Zend\Stdlib\Hydrator\ClassMethods;


class UserObjFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('userobj');
        $this->setHydrator(new ClassMethodsHydrator(false))
             ->setObject(new UserObj());

        
        
        #$this->setLabel('User');
        $this->add(array(
            'name' => 'firstname',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'class'=> 'form-control',
                'id' => 'firstname',
            ),
            'options' => array(
                'label' => 'First Name',
            ),
        ));
         $this->add(array(
            'name' => 'middleinit',          
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'class'=> 'form-control',
                'id' => 'middleinit',
                'maxlength' => 1,
                'size' => 1,
                'style' => 'width:60px;'
            ),
            'options' => array(
                'label' => 'Middle Initial',
            ),
        ));
        $this->add(array(
            'name' => 'email',          
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'class'=> 'form-control',
                'id' => 'email',
            ),
            'options' => array(
                'label' => 'Email',
            ),
        ));
        $this->add(array(
            'name' => 'lastname',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'class'=> 'form-control',
                'id' => 'lastname',
            ),
            'options' => array(
                'label' => 'Last Name',
            ),
        ));
        
        $this->add(array(
            'type' => 'Admin\Form\RoleFieldset',
            'name' => 'roles',
            'options' => array(
                'label' => 'Roles'
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Collection',
            'name' => 'unitprivs',
            'options' => array(
                'label' => 'Assign user to Unit',
                'count' => 1,
                'should_create_template' => true,
                'allow_add' =>true,
                'template_placeholder' => '__placeholder__',
                'target_element' =>array(
                    'type' => 'Admin\Form\UnitPrivFieldset'
                )
            ),
            'attributes' =>array(
                'class' => 'unit-privs'
            )
        ));

    }

    /**
     * @return array
     \*/
    public function getInputFilterSpecification()
    {
        return array(
            'firstname' => array(
                'required' => true,
            ),
            'lastname' => array(
                'required' => true,
            ),
            'middleinit' => array(
                'required' => false,
            ),
             'email' => array(
                'required' => true,
            )
        );
    }
}