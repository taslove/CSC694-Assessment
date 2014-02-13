<?php

 return array(
    
     'view_manager' => array(
         'template_map' => array(
             'partial/unitWidget' => __DIR__ . '/../view/selections/partial/unitWidget.phtml',
         ),
     ),
      
     'view_helpers' => array(
         'invokables' => array(
             'unitWidget' => 'Selections\View\Helper\UnitWidget',
         ),
     ),
 );