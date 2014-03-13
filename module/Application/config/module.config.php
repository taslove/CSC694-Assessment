<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'modules' => array(
        'Application',
        'Admin',
        'Outcomes',
        'Plans',
        'Reports',
        'Review',
        'Mock',
    ),
    'module_listener_options' => array(
            'config_glob_paths'    => array(
                'config/autoload/{,*.}{global,local}.php',
            ),
            'module_paths' => array(
                './module',
                './vendor',
            ),
    ),

    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'literal',
                'options' => array(
                    'route'    => '/index',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',                        
                        'controller' => 'Index',
                        'action'     => 'index',
                        'message' => '',
                    ),
                    'constraints' => array(
                         'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                         'message' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                ),
            ),
            'authenticate' => array(
                 'type'    => 'literal',
                 'options' => array(
                     'route'    => '/index/authenticate',
                     'defaults' => array(
                         '__NAMESPACE__' => 'Application\Controller',
                         'controller' => 'Index',
                         'action'     => 'authenticate',
                     ),
                 ),
             ),
            'logout' => array(
                 'type'    => 'literal',
                 'options' => array(
                     'route'    => '/index/logout',
                     'defaults' => array(
                         '__NAMESPACE__' => 'Application\Controller',
                         'controller' => 'Index',
                         'action'     => 'logout',
                     ),
                 ),
             ),
            'application' => array(
                'type' => 'literal',
                'options' => array(
                    'route'    => '/application',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Application',
                        'action'     => 'index',
                    ),
                ),
            ),
            'choose' => array(
                 'type'    => 'literal',
                 'options' => array(
                     'route'    => '/application/choose',
                     'defaults' => array(
                         '__NAMESPACE__' => 'Application\Controller',
                         'controller' => 'Application',
                         'action'     => 'choose',
                     ),
                 ),
             ),
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Application\Controller\Index'       => 'Application\Controller\IndexController',
            'Application\Controller\Application' => 'Application\Controller\ApplicationController',
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'                  => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index'        => __DIR__ . '/../view/application/index/index.phtml',
            'application/index/authenticate' => __DIR__ . '/../view/application/index/authenticate.phtml',
            'application/application/index ' => __DIR__ . '/../view/application/application/index.phtml',
            'error/404'                      => __DIR__ . '/../view/error/404.phtml',
            'error/index'                    => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),
);
