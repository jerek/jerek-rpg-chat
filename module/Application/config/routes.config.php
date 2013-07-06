<?php

return [
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
    'rooms' => [
        'type' => 'Zend\Mvc\Router\Http\Literal',
        'options' => [
            'route'    => '/rooms',
            'defaults' => [
                'controller' => 'Application\Controller\Room',
                'action'     => 'index',
            ],
        ],
    ],
    'room' => [
        'type' => 'Segment',
        'options' => [
            'route' => '/room/:id',
            'constraints' => [
                'id' => '[0-9]*',
            ],
            'defaults' => [
                'controller' => 'Application\Controller\Room',
                'action' => 'view'
            ],
        ],
        'may_terminate' => true,
        'child_routes' => [
            'roll' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/roll/:sides',
                    'defaults' => [
                        'controller' => 'Application\Controller\Room',
                        'action'     => 'roll',
                    ],
                ],
            ],
            'message' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/message/:message',
                    'defaults' => [
                        'controller' => 'Application\Controller\Room',
                        'action'     => 'message',
                    ],
                ],
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
];
