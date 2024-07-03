<?php

declare(strict_types=1);

namespace Application;

use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\Factory\InvokableFactory;

return [
    'router' => [
        'routes' => [
            'login' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index'
                    ],
                ],
            ],
            'login_principal' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/login',
                    'defaults' => [
                        'controller' => Controller\AccesoController::class,
                        'action'     => 'login'
                    ],
                ],
            ],
            'logout' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/logout',
                    'defaults' => [
                        'controller' => Controller\AccesoController::class,
                        'action'     => 'logout'
                    ],
                ],
            ],
            'acceso' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/acceso[/:action]',
                    'defaults' => [
                        'controller' => Controller\AccesoController::class,
                        'action'     => 'login'
                    ],
                ],
            ],
            'panel' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/panel[/:action]',
                    'defaults' => [
                        'controller' => Controller\PanelController::class,
                        'action'     => 'index'
                    ],
                ],
            ],
            'sistema' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/sistema[/:action]',
                    'defaults' => [
                        'controller' => Controller\SistemaController::class,
                        'action'     => 'index'
                    ],
                ],
            ],
            'herramienta' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/herramienta[/:action]',
                    'defaults' => [
                        'controller' => Controller\HerramientaController::class,
                        'action'     => 'index'
                    ],
                ],
            ],
            'mantenimiento' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/mantenimiento[/:action]',
                    'defaults' => [
                        'controller' => Controller\MantenimientoController::class,
                        'action'     => 'index'
                    ],
                ],
            ],
            'cliente' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/cliente[/:action]',
                    'defaults' => [
                        'controller' => Controller\ClienteController::class,
                        'action'     => 'index'
                    ],
                ],
            ],
            'paginas-ferias-configuracion' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/cliente/paginas-ferias-configuracion[/:idferias][/:idpaginas]',
                    'constraints' => [
                        'idferias' => '[0-9]*',
                        'idpaginas' => '[0-9]*'
                    ],
                    'defaults' => [
                        'controller' => Controller\ClienteController::class,
                        'action'     => 'paginas-ferias-configuracion'
                    ],
                ],
            ],
            'paginas-zonas-configuracion' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/cliente/paginas-zonas-configuracion[/:idzonas]',
                    'constraints' => [
                        'idzonas' => '[0-9]*'
                    ],
                    'defaults' => [
                        'controller' => Controller\ClienteController::class,
                        'action'     => 'paginas-zonas-configuracion'
                    ],
                ],
            ],
            'paginas-stand-configuracion' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/cliente/paginas-stand-configuracion[/:idempresas]',
                    'constraints' => [
                        'idempresas' => '[0-9]*'
                    ],
                    'defaults' => [
                        'controller' => Controller\ClienteController::class,
                        'action'     => 'paginas-stand-configuracion'
                    ],
                ],
            ],
            'producto' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/producto[/:action]',
                    'defaults' => [
                        'controller' => Controller\ProductoController::class,
                        'action'     => 'index'
                    ],
                ],
            ],
            'promocion' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/promocion[/:action]',
                    'defaults' => [
                        'controller' => Controller\PromocionController::class,
                        'action'     => 'index'
                    ],
                ],
            ],
            'oferta' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/oferta[/:action]',
                    'defaults' => [
                        'controller' => Controller\OfertaController::class,
                        'action'     => 'index'
                    ],
                ],
            ],
            'buscador' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/buscador[/:action]',
                    'defaults' => [
                        'controller' => Controller\BuscadorController::class,
                        'action'     => 'index'
                    ],
                ],
            ],
            'plano' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/plano[/:action]',
                    'defaults' => [
                        'controller' => Controller\PlanoController::class,
                        'action'     => 'index'
                    ],
                ],
            ],
            'networking' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/networking[/:action]',
                    'defaults' => [
                        'controller' => Controller\NetworkingController::class,
                        'action'     => 'index'
                    ],
                ],
            ],
            'tools' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/tools[/:action]',
                    'defaults' => [
                        'controller' => Controller\ToolsController::class,
                        'action'     => 'index'
                    ],
                ],
            ],
            'reporte' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/reporte[/:action]',
                    'defaults' => [
                        'controller' => Controller\ReporteController::class,
                        'action'     => 'index'
                    ],
                ],
            ],
        ],
    ],
    'console' => [],
    'controllers' => [
        'aliases' => [
            'index' => \Controller\IndexController::class,
            'acceso' => \Controller\AccesoController::class,
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
            'mail/template'           => __DIR__ . '/../view/application/mail/template.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
        'strategies' => [
            'ViewJsonStrategy',
        ]
    ],
];
