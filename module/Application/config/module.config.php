<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */



return array(
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'application' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/application',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'remove_user_from_all_projects' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/project/remove_user/:userName/:backUrl',
                            'constraints' => array(
                                'userName' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'User',
                                'action' => 'removeUserFromAllProjects',
                            ),
                        ),
                    ),
                    'remove_user_from_project' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/project/:projectName/remove_user/:userName/:backUrl',
                            'constraints' => array(
                                'projectName' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'userName' => '[a-zA-Z][a-zA-Z0-9_-]*',
//                                'backUrl' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Project',
                                'action' => 'removeUserFromProject',
                            ),
                        ),
                    ),
                    'users' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/user/list',
                            'defaults' => array(
                                'controller' => 'User',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'create_project' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/project/create',
                            'defaults' => array(
                                'controller' => 'Project',
                                'action' => 'create',
                            ),
                        ),
                    ),
                    'projects' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/project/list',
                            'defaults' => array(
                                'controller' => 'Project',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'add_user_to_project' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/project/add_user/:backUrl',
                            'constraints' => array(
                                'projectName' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Project',
                                'action' => 'addUserToProject',
                            ),
                        ),
                    ),
                    'user_projects' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/user/:userName/projects',
                            'constraints' => array(
                                'userName' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'User',
                                'action' => 'projects',
                            ),
                        ),
                    ),
                    'project_users' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/project/:projectName/users',
                            'constraints' => array(
                                'projectName' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Project',
                                'action' => 'users',
                            ),
                        ),
                    ),
//                    'default' => array(
//                        'type'    => 'Segment',
//                        'options' => array(
//                            'route'    => '/[:controller[/:action]]',
//                            'constraints' => array(
//                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
//                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
//                            ),
//                            'defaults' => array(
//                            ),
//                        ),
//                    ),
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
            'Application\Controller\Index' => 'Application\Controller\IndexController',
            'Application\Controller\Project' => 'Application\Controller\ProjectController',
            'Application\Controller\User' => 'Application\Controller\UserController',
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
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
