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
                    'scripts/site/chat.js',
                ],
            ],

            'paths' => [
                'application' => __DIR__ . '/../public'
            ],
        ],
    ],

    'router' => [
        'routes' => include __DIR__ . '/routes.config.php',
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

    'spiffy_navigation' => include __DIR__ . '/navigation.config.php',

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
            'Application\Controller\Index' => 'Application\Controller\IndexController',
            'Application\Controller\Room'  => 'Application\Controller\RoomController',
        ],
    ],

    'view_manager' => [
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
