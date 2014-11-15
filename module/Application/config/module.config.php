<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
return array(
    'doctrine' => array(
        'driver' => array(
            'application_entities' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/Application/Entity')
            ),
            'orm_default' => array(
                'drivers' => array(
                    'Application\Entity' => 'application_entities'
                )
            ),
        ),
        'authentication' => array(
            'orm_default' => array(
                'object_manager' => 'Doctrine\ORM\EntityManager',
                'identity_class' => 'Application\Entity\Usuario',
                'identity_property' => 'email',
                'credential_property' => 'senha',
                'credential_callable' => function(\Application\Entity\Usuario $usuario,
                $senha) {
            $bcrypt = new \Zend\Crypt\Password\Bcrypt();
            return $bcrypt->verify($senha, $usuario->getSenha());
        }
            ),
        ),
    ),
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/',
                    'defaults' => array(
                        'controller' => 'Index',
                        'action' => 'index',
                    ),
                ),
            ),
            'assuntos' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/assuntos[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'assuntos',
                        'action' => 'index',
                    ),
                ),
            ),
            'autenticar' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/autenticar',
                    'defaults' => array(
                        'controller' => 'Usuarios',
                        'action' => 'autenticar',
                    ),
                ),
            ),
            'logout' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/logout',
                    'defaults' => array(
                        'controller' => 'Usuarios',
                        'action' => 'logout',
                    ),
                ),
            ),
            'orgaosexternos' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/orgaosexternos[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'OrgaosExternos',
                        'action' => 'index',
                    ),
                ),
            ),
            'processos' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/processos[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Processos',
                        'action' => 'index',
                    ),
                ),
            ),
            'secretarias' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/secretarias[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Secretarias',
                        'action' => 'index',
                    ),
                ),
            ),
            'setores' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/setores[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Setores',
                        'action' => 'index',
                    ),
                ),
            ),
            'tiposdesetor' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/tiposdesetor[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'TiposDeSetor',
                        'action' => 'index',
                    ),
                ),
            ),
            'tiposdedocumento' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/tiposdedocumento[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'TiposDeDocumento',
                        'action' => 'index',
                    ),
                ),
            ),
            'usuarios' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/usuarios[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Usuarios',
                        'action' => 'index',
                    ),
                ),
            ),
            'workflows' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/workflows[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Workflows',
                        'action' => 'index',
                    ),
                ),
            ),
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'application' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/app',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Index',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
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
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Index' => 'Application\Controller\IndexController',
            'Assuntos' => 'Application\Controller\ManterAssuntosController',
            'OrgaosExternos' => 'Application\Controller\ManterOrgaosExternosController',
            'Processos' => 'Application\Controller\ManterProcessosController',
            'Secretarias' => 'Application\Controller\ManterSecretariasController',
            'Setores' => 'Application\Controller\ManterSetoresController',
            'TiposDeDocumento' => 'Application\Controller\ManterTiposDeDocumentoController',
            'TiposDeSetor' => 'Application\Controller\ManterTiposDeSetorController',
            'Usuarios' => 'Application\Controller\ManterUsuariosController',
            'Workflows' => 'Application\Controller\ManterWorkflowsController',
        ),
    ),
    'controller_plugins' => array(
        'invokables' => array(
            'AclPlugin' => 'Application\Controller\Plugin\AclPlugin',
//            'LogPlugin' => 'Application\Controller\Plugin\LogPlugin',
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => array(
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml',
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
