<?php
namespace Admin\Form;

use Admin\Entity\UnitPriv;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class UnitPrivFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('unitpriv');
        $this->setHydrator(new ClassMethodsHydrator(false))
             ->setObject(new UnitPriv());

        #$this->setLabel('Unit Priv');

         $this->add(array(
            'name' => 'unit_id ',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'class'=> 'form-control unit-privs',
                'id' => 'unit_id',
            ),
            'options' => array(
                'value_options' => array(
                    'ACC'=>'ACC',
                    'BIO'=>'BIO',
                    'CSC'=>'CSC',
                    'HST'=>'HST',
                    'MTH'=>'MTH',
                    'PHL'=>'PHL'
                ),
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
                'required' => false,
            )
        );
    }
}