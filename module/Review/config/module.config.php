<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

 return array(
     'controllers' => array(
         'invokables' => array(
             'Review\Controller\Review' => 'Review\Controller\ReviewController',
         ),
     ),
     'view_manager' => array(
         'template_path_stack' => array(
            'review' => __DIR__ . '/../view',
         ),
         'strategies' => array(
            'ViewJsonStrategy',
        ),
     ),
       'router' => array(
         'routes' => array(
             'review' => array(
                 'type'    => 'segment',
                 'options' => array(
                     'route'    => '/review[/][:action][/:id]',
                     'constraints' => array(
                         'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                      ),
                     'defaults' => array(
                         'controller' => 'Review\Controller\Review',
                         /*added action so we don't have to type "/index" */
                         'action' => 'index',
                     ),
                 ),
             ),
         ),
     ),
    
   
 );