<?php

namespace Plans\Form;

use Zend\InputFilter;
use Zend\Form\Form;
use Zend\Form\Element;

class CollectionUpload extends Form
{
    public $numFileElements = 5;

    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);
        $this->addElements();
        $this->setInputFilter($this->createInputFilter());
    }

    public function addElements()
    {
        // File Input
        $file = new Element\File('file');
        $file->setLabel('  ');

        $fileCollection = new Element\Collection('file-collection');
        $fileCollection->setOptions(array(
             'count'          => $this->numFileElements,
             'allow_add'      => false,
             'allow_remove'   => false,
             'target_element' => $file,
        ));
        $this->add($fileCollection);
        
        
        // Text Input
        $text = new Element\Text('text');
        $text->setLabel('File Description');

        $textCollection = new Element\Collection('text-collection');
        $textCollection->setOptions(array(
             'count'          => $this->numFileElements,
             'allow_add'      => false,
             'allow_remove'   => false,
             'target_element' => $text,
        ));
        $this->add($textCollection);

    }

    public function createInputFilter()
    {
        $inputFilter = new InputFilter\InputFilter();

        // File Collection
        $fileCollection = new InputFilter\InputFilter();
        $textCollection = new InputFilter\InputFilter();
        for ($i = 0; $i < $this->numFileElements; $i++) {
            $file = new InputFilter\FileInput($i);
            $file->setRequired(false);
            $file->getFilterChain()->attachByName(
                'filerenameupload',
                array(
                    'target'          => './data/tmpuploads/',
                    'overwrite'       => true,
                    'use_upload_name' => true,
                )
            );
            $fileCollection->add($file);
            
            // Text Collection
            $text = new InputFilter\Input($i);
            $text->setRequired(false);
            $textCollection->add($text);
        }
        $inputFilter->add($fileCollection, 'file-collection');
        $inputFilter->add($textCollection, 'text-collection');
        
        return $inputFilter;
    }
}