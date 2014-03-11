<?php
namespace Admin\Form;

use Admin\Entity\Role;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class RoleFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('roles');
        $this->setHydrator(new ClassMethodsHydrator(false))
             ->setObject(new Role());

        #$this->setLabel('Role');

        $this->add(array(
            'name' => 'name',
            'type' => 'Zend\Form\Element\MultiCheckbox',
            'attributes' => array(
                'id' => 'name',
            ),
            'options' => array(
                'value_options' => array(          
                    '1' => 'Admin',
                    '2' => 'Liason',
                    '3' => 'Chair',
                    '4' => 'Assessor',
                    '5' => 'User',),
            ),
        ));

    }

    /**
     * @return array
     \*/
    public function getInputFilterSpecification()
    {
        return array(
            'name' => array(
                'required' => true,
            )
        );
    }
}