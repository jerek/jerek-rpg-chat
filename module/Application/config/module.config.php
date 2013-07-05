<?php

return [
    'application' => [
        'configuration' => [
            'title'             => 'RPG Chat',
            'googleAnalyticsId' => 'UA-448886-12',
            'domain'            => 'blatcave.net',
            'description'       => 'RPG Chat is a chat and roll logging platform.',
            'keywords'          => 'RPG, dice, dice roller, chat',
        ],
    ],

    'asset_manager' => [
        'resolver_configs' => [
            'collections' => [
                'styles/style.min.css' => [
                    'styles/site/style.less',
                ],
                'scripts/script.min.js' => [
                    'scripts/site/base.js',
                ],
            ],

            'paths' => [
                'application' => __DIR__ . '/../public'
            ],
        ],
    ],

    'router' => [
        'routes' => [
            'home' => [
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'index',
                    ],
                ],
            ],
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'application' => [
                'type'    => 'Literal',
                'options' => [
                    'route'    => '/application',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'default' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/[:controller[/:action]]',
                            'constraints' => [
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                            'defaults' => [
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],

    'service_manager' => [
        'aliases' => [
            'translator' => 'MvcTranslator',
        ],
    ],

    'doctrine' => [
        'driver' => [
            'application_entities' => [
                'paths' => [__DIR__ . '/../src/Application/Entity'],
            ],
        ],
    ],

    'translator' => [
        'locale' => 'en_US',
        'translation_file_patterns' => [
            [
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ],
        ],
    ],

    'controllers' => [
        'invokables' => [
            'Application\Controller\Index' => 'Application\Controller\IndexController'
        ],
    ],

    'view_manager' => [
        'template_map' => [
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],

    'jerekbase' => [
        'less' => [
            'paths' => [
                __DIR__ . '/../public/styles'
            ],
        ],
    ],
];
