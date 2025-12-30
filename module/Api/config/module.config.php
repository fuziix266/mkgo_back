<?php

namespace Api;

use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'api' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/api',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action' => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'auth' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/auth[/:action]',
                            'defaults' => [
                                'controller' => Controller\AuthController::class,
                                'action' => 'login',
                            ],
                        ],
                    ],
                    'vehicles' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/vehicles[/:action]',
                            'defaults' => [
                                'controller' => Controller\VehiclesController::class,
                                'action' => 'index',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => Controller\Factory\IndexControllerFactory::class,
            Controller\AuthController::class => Controller\Factory\AuthControllerFactory::class,
            Controller\VehiclesController::class => Controller\Factory\VehiclesControllerFactory::class,
        ],
    ],
    'view_manager' => [
        'strategies' => [
            'ViewJsonStrategy',
        ],
    ],
];
