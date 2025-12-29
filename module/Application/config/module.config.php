<?php

namespace Application;

return [
    'router' => [
        'routes' => [
            // Las rutas principales están en el módulo Inicio
        ],
    ],
    'controllers' => [
        'factories' => [
            // Los controladores están en los módulos específicos
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'application' => __DIR__ . '/../view',
        ],
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'doctype' => 'HTML5',
        'template_map' => [
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml',
        ],
    ],
    'navigation' => [
        'default' => [
            [
                'label' => 'Inicio',
                'route' => 'home',
            ],
        ],
    ],
];