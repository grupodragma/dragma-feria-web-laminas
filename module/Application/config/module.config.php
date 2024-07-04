<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\Factory\InvokableFactory;

return [
    'router' => [
        'routes' => [
            'home' => [
                'type'      => Segment::class,
                'options'   => [
                    'route'    => '/[:lang][/:action]',
                    'constraints' => ['lang' => 'es|en|pt|zh'],
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'registro' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/[:lang]/registro',
                    'constraints' => ['lang' => 'es|en|pt|zh'],
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'registro',
                    ],
                ],
            ],
            'visitante' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/[:lang]/visitante',
                    'constraints' => ['lang' => 'es|en|pt|zh'],
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'visitante',
                    ],
                ],
            ],
            'acceso' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/acceso[/:action]',
                    'defaults' => [
                        'controller' => Controller\AccesoController::class,
                        'action'     => 'index'
                    ],
                ],
            ],
            'login' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/login',
                    'defaults' => [
                        'controller' => Controller\AccesoController::class,
                        'action'     => 'login'
                    ],
                ],
            ],
            'login-presencial' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/login-presencial',
                    'defaults' => [
                        'controller' => Controller\AccesoController::class,
                        'action'     => 'login-presencial'
                    ],
                ],
            ],
            'login-recepcion' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/login-recepcion',
                    'defaults' => [
                        'controller' => Controller\AccesoController::class,
                        'action'     => 'login-recepcion'
                    ],
                ],
            ],
            'generar-csrf-token' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/generar-csrf-token',
                    'defaults' => [
                        'controller' => Controller\AccesoController::class,
                        'action'     => 'generar-csrf-token'
                    ],
                ],
            ],
            'logout' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/[:lang]/logout',
                    'constraints' => ['lang' => 'es|en|pt|zh'],
                    'defaults' => [
                        'controller' => Controller\AccesoController::class,
                        'action'     => 'logout'
                    ],
                ],
            ],
            'panel' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/[:lang]/panel[/:action]',
                    'constraints' => ['lang' => 'es|en|pt|zh'],
                    'defaults' => [
                        'controller' => Controller\PanelController::class,
                        'action'     => 'index'
                    ],
                ],
            ],
            'panel-zonas' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/[:lang]/panel/zonas[/:ordenzona]',
                    'constraints' => array(
                        'lang' => 'es|en|pt|zh',
                        'ordenzona' => '[0-9]*'
                    ),
                    'defaults' => [
                        'controller' => Controller\PanelController::class,
                        'action'     => 'zonas'
                    ],
                ],
            ],
            'panel-empresas' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/[:lang]/panel/empresas[/:url][/:idzonas]',
                    'constraints' => array(
                        'lang' => 'es|en|pt|zh',
                        'url' => '[-a-zA-Z]+',
                        'idzonas' => '[0-9]*'
                    ),
                    'defaults' => [
                        'controller' => Controller\PanelController::class,
                        'action'     => 'empresas'
                    ],
                ],
            ],
            'panel-expositores-vivo' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/[:lang]/panel/expositores-vivo[/:zona][/:ordenzona][/:empresa][/:ordenempresa][/:hashurlempresa]',
                    'constraints' => array(
                        'lang' => 'es|en|pt|zh',
                        'zona' => 'zona',
                        'ordenzona' => '[0-9]*',
                        'empresa' => 'empresa',
                        'ordenempresa' => '[0-9]*',
                        'hashurlempresa' => '[a-zA-Z][a-zA-Z0-9_-]*'
                    ),
                    'defaults' => [
                        'controller' => Controller\PanelController::class,
                        'action'     => 'expositores-vivo'
                    ],
                ],
            ],
            'panel-stand' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/[:lang]/panel/stand[/:zona][/:ordenzona][/:empresa][/:ordenempresa][/:hashurlempresa]',
                    'constraints' => array(
                        'lang' => 'es|en|pt|zh',
                        'zona' => 'zona',
                        'ordenzona' => '[0-9]*',
                        'empresa' => 'empresa',
                        'ordenempresa' => '[0-9]*',
                        'hashurlempresa' => '[a-zA-Z][a-zA-Z0-9_-]*'
                    ),
                    'defaults' => [
                        'controller' => Controller\PanelController::class,
                        'action'     => 'stand'
                    ],
                ],
            ],
            'panel-realidad-virtual' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/[:lang]/panel/realidad-virtual[/:idempresa]',
                    'constraints' => array(
                        'lang' => 'es|en|pt|zh',
                        'idempresa' => '[0-9]*'
                    ),
                    'defaults' => [
                        'controller' => Controller\PanelController::class,
                        'action'     => 'realidad-virtual'
                    ],
                ],
            ],
            'panel-productos' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/[:lang]/panel/productos[/:zona][/:ordenzona][/:empresa][/:ordenempresa][/:hashurlempresa]',
                    'constraints' => array(
                        'lang' => 'es|en|pt|zh',
                        'zona' => 'zona',
                        'ordenzona' => '[0-9]*',
                        'empresa' => 'empresa',
                        'ordenempresa' => '[0-9]*',
                        'hashurlempresa' => '[a-zA-Z][a-zA-Z0-9_-]*'
                    ),
                    'defaults' => [
                        'controller' => Controller\PanelController::class,
                        'action'     => 'productos'
                    ],
                ],
            ],
            'panel-planos' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/[:lang]/panel/planos[/:zona][/:ordenzona][/:empresa][/:ordenempresa][/:hashurlempresa]',
                    'constraints' => array(
                        'lang' => 'es|en|pt|zh',
                        'zona' => 'zona',
                        'ordenzona' => '[0-9]*',
                        'empresa' => 'empresa',
                        'ordenempresa' => '[0-9]*',
                        'hashurlempresa' => '[a-zA-Z][a-zA-Z0-9_-]*'
                    ),
                    'defaults' => [
                        'controller' => Controller\PanelController::class,
                        'action'     => 'planos'
                    ],
                ],
            ],
            'panel-promociones' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/[:lang]/panel/promociones[/:zona][/:ordenzona][/:empresa][/:ordenempresa][/:hashurlempresa]',
                    'constraints' => array(
                        'lang' => 'es|en|pt|zh',
                        'zona' => 'zona',
                        'ordenzona' => '[0-9]*',
                        'empresa' => 'empresa',
                        'ordenempresa' => '[0-9]*',
                        'hashurlempresa' => '[a-zA-Z][a-zA-Z0-9_-]*'
                    ),
                    'defaults' => [
                        'controller' => Controller\PanelController::class,
                        'action'     => 'promociones'
                    ],
                ],
            ],
            'panel-auditorio' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/[:lang]/panel/auditorio[/:idcronograma]',
                    'constraints' => array(
                        'lang' => 'es|en|pt|zh',
                        'idcronograma' => '[0-9]*'
                    ),
                    'defaults' => [
                        'controller' => Controller\PanelController::class,
                        'action'     => 'auditorio'
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
            /*'panel-networking' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/[:lang]/panel/networking[/:zona][/:ordenzona][/:empresa][/:ordenempresa][/:hashurlempresa]',
                    'constraints' => array(
                        'lang' => 'es|en|pt|zh',
                        'zona' => 'zona',
                        'ordenzona' => '[0-9]*',
                        'empresa' => 'empresa',
                        'ordenempresa' => '[0-9]*',
                        'hashurlempresa' => '[a-zA-Z][a-zA-Z0-9_-]*'
                    ),
                    'defaults' => [
                        'controller' => Controller\PanelController::class,
                        'action'     => 'networking'
                    ],
                ],
            ],*/
            'panel-networking' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/[:lang]/panel/networking[/:id]',
                    'constraints' => array(
                        'lang' => 'es|en|pt|zh',
                        'id' => '[0-9]*'
                    ),
                    'defaults' => [
                        'controller' => Controller\PanelController::class,
                        'action'     => 'networking'
                    ],
                ],
            ],
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
            'mail/template-visitante' => __DIR__ . '/../view/application/mail/template-visitante.phtml',
            'mail/template-producto' => __DIR__ . '/../view/application/mail/template-producto.phtml',
            'mail/template-promocion' => __DIR__ . '/../view/application/mail/template-promocion.phtml',
            'mail/template-banco' => __DIR__ . '/../view/application/mail/template-banco.phtml',
            'mail/template-expositor' => __DIR__ . '/../view/application/mail/template-expositor.phtml',
            'mail/template-agenda-virtual' => __DIR__ . '/../view/application/mail/template-agenda-virtual.phtml',
            'mail/template-chat-notificacion' => __DIR__ . '/../view/application/mail/template-chat-notificacion.phtml'
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
        'strategies' => [
            'ViewJsonStrategy',
        ]
    ]
];
