<?php

namespace Admin\Form;

use Zend\Form\Form;

class ProgramForm extends Form
{
    public function __construct($name = null)
    {

        parent::__construct('queries');
       
    }
}