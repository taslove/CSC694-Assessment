<?php
namespace Admin\Form;

use Admin\Entity\UserObj;
#use Admin\Model\User;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class UserFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('userobj');
        $this->setHydrator(new ClassMethodsHydrator(false))
             ->setObject(new UserObj());

        $this->setLabel('User');

        $this->add(array(
            'name' => 'firstname',
            'options' => array(
                'label' => 'First Name'
            ),
        ));
        $this->add(array(
            'name' => 'lastname',
            'options' => array(
                'label' => 'Last Name'
            ),
        ));
        $this->add(array(
            'name' => 'middleinit',
            'options' => array(
                'label' => 'Middle Init'
            ),
        ));
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Collection',
            'name' => 'roles',
            'options' => array(
                'count' => 1,
                'should_create_template' => false,
                'allow_add' => false,
                'target_element' => array(
                    'type' => 'Admin\Form\RoleFieldset'
                )
            )
        ));
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Collection',
            'name' => 'unitprivs',
            'options' => array(
                'label' => 'Please choose categories for this product',
                'count' => 1,
                'should_create_template' => true,
                'allow_add' => true,
                'target_element' => array(
                    'type' => 'Admin\Form\UnitPrivFieldset'
                )
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
            )
        );
    }
}