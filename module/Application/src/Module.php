<?php

/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

declare(strict_types=1);

namespace Application;

use Laminas\Mvc\MvcEvent;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\TableGateway\TableGateway;
use Laminas\ModuleManager\Feature\ConfigProviderInterface;
use Laminas\Authentication\Storage\Session as SessionStorage;
use Laminas\Authentication\AuthenticationService;
use Laminas\Session\SessionManager;
use Application\Service\AuthManager;
use Application\Service\Uploader;
use Application\Helper\Tools;

class Module implements ConfigProviderInterface
{
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function onBootstrap($e)
    {
        if (isset($_SERVER['HTTP_HOST'])) {
            $protocol = (! empty($_SERVER['HTTPS'])) ? 'https' : 'http';
            $application = $e->getParam('application');
            $viewModel = $application->getMvcEvent()->getViewModel();
            $services = $application->getServiceManager();
            $config = $services->get('Config');
            $viewModel->base_url = $protocol . '://' . $_SERVER['HTTP_HOST'];
            $viewModel->node_server = $config['node_server'];
        }

        $eventManager = $e->getApplication()->getEventManager();
        $sharedEventManager = $eventManager->getSharedManager();
        $sharedEventManager->attach(AbstractActionController::class, MvcEvent::EVENT_DISPATCH, [$this, 'onDispatch'], 100);
        $sharedEventManager->attach('Laminas\Mvc\Application', MvcEvent::EVENT_DISPATCH_ERROR, [$this, 'onDispatchError'], 100);
        $sessionManager = $e->getApplication()->getServiceManager()->get(\Laminas\Session\SessionManager::class);
        $this->forgetInvalidSession($sessionManager);
    }

    private function forgetInvalidSession($sessionManager)
    {
        try {
            $sessionManager->start();
            return;
        } catch (\RuntimeException $e) {
        }
        session_unset();
    }

    public function getServiceConfig()
    {
        return [
            'factories' => [
                Model\AccesoTable::class => function ($container) {

                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('fd_usuarios', $adapter);
                    $table = new Model\AccesoTable($tableGateway);
                    return $table;
                },
                Model\UsuarioTable::class => function ($container) {

                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('fd_usuarios', $adapter);
                    $table = new Model\UsuarioTable($tableGateway);
                    return $table;
                },
                Model\PerfilesTable::class => function ($container) {

                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('fd_perfiles', $adapter);
                    $table = new Model\PerfilesTable($tableGateway);
                    return $table;
                },
                Model\GruposTable::class => function ($container) {

                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('fd_grupo', $adapter);
                    $table = new Model\GruposTable($tableGateway);
                    return $table;
                },
                Model\ModulosTable::class => function ($container) {

                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('fd_modulos', $adapter);
                    $table = new Model\ModulosTable($tableGateway, $adapter);
                    return $table;
                },
                Model\SubModulosTable::class => function ($container) {

                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('fd_submodulos', $adapter);
                    $table = new Model\SubModulosTable($tableGateway, $adapter);
                    return $table;
                },
                Model\VisitantesTable::class => function ($container) {

                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('visitantes', $adapter);
                    $table = new Model\VisitantesTable($tableGateway, $adapter);
                    return $table;
                },
                Model\PlanesTable::class => function ($container) {

                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('planes', $adapter);
                    $table = new Model\PlanesTable($tableGateway, $adapter);
                    return $table;
                },
                Model\FeriasTable::class => function ($container) {

                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('ferias', $adapter);
                    $table = new Model\FeriasTable($tableGateway, $adapter);
                    return $table;
                },
                Model\ClientesTable::class => function ($container) {

                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('clientes', $adapter);
                    $table = new Model\ClientesTable($tableGateway, $adapter);
                    return $table;
                },
                Model\ZonasTable::class => function ($container) {

                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('zonas', $adapter);
                    $table = new Model\ZonasTable($tableGateway, $adapter);
                    return $table;
                },
                Model\StandTable::class => function ($container) {

                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('stand', $adapter);
                    $table = new Model\StandTable($tableGateway, $adapter);
                    return $table;
                },
                Model\StandModeloTable::class => function ($container) {

                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('stand_modelo', $adapter);
                    $table = new Model\StandModeloTable($tableGateway, $adapter);
                    return $table;
                },
                Model\PaginasTable::class => function ($container) {

                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('paginas', $adapter);
                    $table = new Model\PaginasTable($tableGateway, $adapter);
                    return $table;
                },
                Model\CronogramasTable::class => function ($container) {

                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('cronogramas', $adapter);
                    $table = new Model\CronogramasTable($tableGateway, $adapter);
                    return $table;
                },
                Model\ExpositoresTable::class => function ($container) {

                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('expositores', $adapter);
                    $table = new Model\ExpositoresTable($tableGateway, $adapter);
                    return $table;
                },
                Model\EmpresasTable::class => function ($container) {

                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('empresas', $adapter);
                    $table = new Model\EmpresasTable($tableGateway, $adapter);
                    return $table;
                },
                Model\PaginasFeriasTable::class => function ($container) {

                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('paginas_ferias', $adapter);
                    $table = new Model\PaginasFeriasTable($tableGateway, $adapter);
                    return $table;
                },
                Model\PaginasZonasTable::class => function ($container) {

                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('paginas_zonas', $adapter);
                    $table = new Model\PaginasZonasTable($tableGateway, $adapter);
                    return $table;
                },
                Model\PaginasStandTable::class => function ($container) {

                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('paginas_stand', $adapter);
                    $table = new Model\PaginasStandTable($tableGateway, $adapter);
                    return $table;
                },
                Model\ProductosTable::class => function ($container) {

                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('productos', $adapter);
                    $table = new Model\ProductosTable($tableGateway, $adapter);
                    return $table;
                },

                Model\PromocionesTable::class => function ($container) {

                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('promociones', $adapter);
                    $table = new Model\PromocionesTable($tableGateway, $adapter);
                    return $table;
                },
                Model\OfertasTable::class => function ($container) {

                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('ofertas', $adapter);
                    $table = new Model\OfertasTable($tableGateway, $adapter);
                    return $table;
                },
                Model\ConferenciasTable::class => function ($container) {

                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('conferencias', $adapter);
                    $table = new Model\ConferenciasTable($tableGateway, $adapter);
                    return $table;
                },
                Model\ExpositoresConferenciasTable::class => function ($container) {

                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('expositores_conferencias', $adapter);
                    $table = new Model\ExpositoresConferenciasTable($tableGateway, $adapter);
                    return $table;
                },
                Model\PlanesPaginasTable::class => function ($container) {

                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('planes_paginas', $adapter);
                    $table = new Model\PlanesPaginasTable($tableGateway, $adapter);
                    return $table;
                },
                Model\MenusTable::class => function ($container) {

                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('menus', $adapter);
                    $table = new Model\MenusTable($tableGateway, $adapter);
                    return $table;
                },
                Model\PlanesMenusTable::class => function ($container) {

                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('planes_menus', $adapter);
                    $table = new Model\PlanesMenusTable($tableGateway, $adapter);
                    return $table;
                },
                Model\SectoresTable::class => function ($container) {

                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('sectores', $adapter);
                    $table = new Model\SectoresTable($tableGateway, $adapter);
                    return $table;
                },
                Model\ExpositoresProductosTable::class => function ($container) {

                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('expositores_productos', $adapter);
                    $table = new Model\ExpositoresProductosTable($tableGateway, $adapter);
                    return $table;
                },
                Model\SectoresGaleriaTable::class => function ($container) {

                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('sectores_galeria', $adapter);
                    $table = new Model\SectoresGaleriaTable($tableGateway, $adapter);
                    return $table;
                },
                Model\StandGaleriaTable::class => function ($container) {

                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('stand_galeria', $adapter);
                    $table = new Model\StandGaleriaTable($tableGateway, $adapter);
                    return $table;
                },
                Model\ExpositoresTarjetasTable::class => function ($container) {

                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('expositores_tarjetas', $adapter);
                    $table = new Model\ExpositoresTarjetasTable($tableGateway, $adapter);
                    return $table;
                },
                Model\UsuarioEventosTable::class => function ($container) {

                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('usuario_eventos', $adapter);
                    $table = new Model\UsuarioEventosTable($tableGateway, $adapter);
                    return $table;
                },
                Model\BuscadorTable::class => function ($container) {

                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('buscador', $adapter);
                    $table = new Model\BuscadorTable($tableGateway, $adapter);
                    return $table;
                },
                Model\BancosTable::class => function ($container) {

                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('bancos', $adapter);
                    $table = new Model\BancosTable($tableGateway, $adapter);
                    return $table;
                },
                Model\ProductosImagenesTable::class => function ($container) {

                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('productos_imagenes', $adapter);
                    $table = new Model\ProductosImagenesTable($tableGateway, $adapter);
                    return $table;
                },
                Model\PlanosTable::class => function ($container) {

                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('planos', $adapter);
                    $table = new Model\PlanosTable($tableGateway, $adapter);
                    return $table;
                },
                Model\PlataformasTable::class => function ($container) {

                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('plataformas', $adapter);
                    $table = new Model\PlataformasTable($tableGateway, $adapter);
                    return $table;
                },
                Model\SpeakersTable::class => function ($container) {

                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('speakers', $adapter);
                    $table = new Model\SpeakersTable($tableGateway, $adapter);
                    return $table;
                },
                Model\SpeakersConferenciasTable::class => function ($container) {

                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('speakers_conferencias', $adapter);
                    $table = new Model\SpeakersConferenciasTable($tableGateway, $adapter);
                    return $table;
                },
                Model\SalasTable::class => function ($container) {

                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('salas', $adapter);
                    $table = new Model\SalasTable($tableGateway, $adapter);
                    return $table;
                },
                Model\ChatsTable::class => function ($container) {

                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('chats', $adapter);
                    $table = new Model\ChatsTable($tableGateway, $adapter);
                    return $table;
                },
                Model\SeoTable::class => function ($container) {

                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('seo', $adapter);
                    $table = new Model\SeoTable($tableGateway, $adapter);
                    return $table;
                },
                Model\FeriasPasosTable::class => function ($container) {

                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('ferias_pasos', $adapter);
                    return new Model\FeriasPasosTable($tableGateway, $adapter);
                },
                Model\PaginasBotonesTable::class => function ($container) {

                    $config = $container->get('Config');
                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('paginas_botones', $adapter);
                    return new Model\PaginasBotonesTable($tableGateway, $adapter, $config);
                },
                Model\FeriasCorreosTable::class => function ($container) {

                    $config = $container->get('Config');
                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('ferias_correos', $adapter);
                    return new Model\FeriasCorreosTable($tableGateway, $adapter, $config);
                },
                Model\AgendaVirtualTable::class => function ($container) {

                    $config = $container->get('Config');
                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('agenda_virtual', $adapter);
                    return new Model\AgendaVirtualTable($tableGateway, $adapter, $config);
                },
                Model\FeriasPromocionesTable::class => function ($container) {

                    $config = $container->get('Config');
                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('ferias_promociones', $adapter);
                    return new Model\FeriasPromocionesTable($tableGateway, $adapter, $config);
                },
                Model\VisitantesRegistrosTable::class => function ($container) {

                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('visitantes_registros', $adapter);
                    return new Model\VisitantesRegistrosTable($tableGateway, $adapter);
                },
                \Laminas\Authentication\AuthenticationService::class => function ($container) {

                    $sessionManager = $container->get(\Laminas\Session\SessionManager::class);
                    $authStorage = new SessionStorage('Zend_Auth', 'session', $sessionManager);
                    $authAdapter = $container->get(Service\AuthAdapter::class);
                    return new AuthenticationService($authStorage, $authAdapter);
                },
                Service\AuthAdapter::class => function ($container) {

                    $sessionContainer = $container->get('DatosSession');
                    return new Service\AuthAdapter($container->get(Model\AccesoTable::class), $sessionContainer);
                },
                Service\AuthManager::class => function ($container) {

                    $authenticationService = $container->get(\Laminas\Authentication\AuthenticationService::class);
                    $sessionManager = $container->get(SessionManager::class);
                    return new AuthManager($authenticationService, $sessionManager);
                },
                Service\Uploader::class => function () {

                    return new Uploader();
                },
                Helper\Tools::class => function ($container) {

                    return new Tools($container);
                }
            ],
        ];
    }

    public function getControllerConfig()
    {
        return [
            'factories' => [
                Controller\AccesoController::class => fn($container) => new Controller\AccesoController($container->get(Service\AuthManager::class), $container->get(Model\AccesoTable::class)),
                Controller\IndexController::class => fn($container) => new Controller\IndexController($container, $container->get(Model\AccesoTable::class)),
                Controller\PanelController::class => fn($container) => new Controller\PanelController(
                    $container,
                    $container->get(Model\AccesoTable::class),
                    $container->get(Model\UsuarioTable::class),
                    $container->get(Service\Uploader::class),
                    $container->get(Model\SectoresGaleriaTable::class),
                    $container->get(Model\StandGaleriaTable::class),
                    $container->get(Model\ZonasTable::class),
                    $container->get(Model\EmpresasTable::class),
                    $container->get(Model\FeriasTable::class),
                    $container->get(Model\UsuarioEventosTable::class),
                    $container->get(Model\VisitantesTable::class)
                ),
                Controller\SistemaController::class => fn($container) => new Controller\SistemaController(
                    $container,
                    $container->get(Model\UsuarioTable::class),
                    $container->get(Model\PerfilesTable::class),
                    $container->get(Service\Uploader::class),
                    $container->get(Model\ModulosTable::class),
                    $container->get(Model\SubModulosTable::class),
                    $container->get(Model\FeriasTable::class)
                ),
                Controller\HerramientaController::class => fn($container) => new Controller\HerramientaController(
                    $container,
                    $container->get(Model\ProductosTable::class)
                ),
                Controller\MantenimientoController::class => fn($container) => new Controller\MantenimientoController(
                    $container,
                    $container->get(Model\PlanesTable::class),
                    $container->get(Model\StandTable::class),
                    $container->get(Model\StandModeloTable::class),
                    $container->get(Model\PaginasTable::class),
                    $container->get(Model\PlanesPaginasTable::class),
                    $container->get(Model\MenusTable::class),
                    $container->get(Model\PlanesMenusTable::class),
                    $container->get(Model\SectoresTable::class),
                    $container->get(Model\BancosTable::class),
                    $container->get(Model\PlataformasTable::class),
                    $container->get(Helper\Tools::class)
                ),
                Controller\ClienteController::class => fn($container) => new Controller\ClienteController(
                    $container,
                    $container->get(Model\VisitantesTable::class),
                    $container->get(Model\FeriasTable::class),
                    $container->get(Model\ClientesTable::class),
                    $container->get(Model\PlanesTable::class),
                    $container->get(Model\ZonasTable::class),
                    $container->get(Model\CronogramasTable::class),
                    $container->get(Model\ExpositoresTable::class),
                    $container->get(Model\EmpresasTable::class),
                    $container->get(Model\StandTable::class),
                    $container->get(Model\PaginasFeriasTable::class),
                    $container->get(Model\PaginasZonasTable::class),
                    $container->get(Model\PaginasStandTable::class),
                    $container->get(Model\PaginasTable::class),
                    $container->get(Model\ConferenciasTable::class),
                    $container->get(Model\ExpositoresConferenciasTable::class),
                    $container->get(Model\PlanesPaginasTable::class),
                    $container->get(Model\SectoresTable::class),
                    $container->get(Model\ExpositoresProductosTable::class),
                    $container->get(Model\StandGaleriaTable::class),
                    $container->get(Model\ExpositoresTarjetasTable::class),
                    $container->get(Model\BancosTable::class),
                    $container->get(Helper\Tools::class),
                    $container->get(Model\PlataformasTable::class),
                    $container->get(Model\SpeakersTable::class),
                    $container->get(Model\SpeakersConferenciasTable::class),
                    $container->get(Model\SalasTable::class),
                    $container->get(Model\ChatsTable::class),
                    $container->get(Model\MenusTable::class),
                    $container->get(Model\SeoTable::class),
                    $container->get(Model\FeriasPasosTable::class),
                    $container->get(Model\PaginasBotonesTable::class),
                    $container->get(Model\FeriasCorreosTable::class),
                    $container->get(Model\AgendaVirtualTable::class),
                    $container->get(Model\FeriasPromocionesTable::class),
                    $container->get(Model\PromocionesTable::class)
                ),
                Controller\ProductoController::class => fn($container) => new Controller\ProductoController(
                    $container,
                    $container->get(Model\ProductosTable::class),
                    $container->get(Model\EmpresasTable::class),
                    $container->get(Model\ProductosImagenesTable::class)
                ),
                Controller\PromocionController::class => fn($container) => new Controller\PromocionController(
                    $container,
                    $container->get(Model\PromocionesTable::class),
                    $container->get(Model\EmpresasTable::class)
                ),
                Controller\OfertaController::class => fn($container) => new Controller\OfertaController(
                    $container,
                    $container->get(Model\OfertasTable::class),
                    $container->get(Model\EmpresasTable::class)
                ),
                Controller\BuscadorController::class => fn($container) => new Controller\BuscadorController(
                    $container,
                    $container->get(Model\BuscadorTable::class),
                    $container->get(Model\FeriasTable::class),
                    $container->get(Model\EmpresasTable::class)
                ),
                Controller\PlanoController::class => fn($container) => new Controller\PlanoController(
                    $container,
                    $container->get(Model\PlanosTable::class),
                    $container->get(Model\EmpresasTable::class)
                ),
                Controller\NetworkingController::class => fn($container) => new Controller\NetworkingController($container),
                Controller\ToolsController::class => fn($container) => new Controller\ToolsController(
                    $container,
                    $container->get(Helper\Tools::class),
                    $container->get(Model\EmpresasTable::class),
                    $container->get(Model\VisitantesTable::class),
                    $container->get(Model\UsuarioEventosTable::class)
                ),
                Controller\ReporteController::class => fn($container) => new Controller\ReporteController(
                    $container,
                    $container->get(Model\VisitantesRegistrosTable::class)
                )
            ],
        ];
    }

    public function onDispatch(MvcEvent $event)
    {
        $container = $event->getApplication()->getserviceManager();
        try {
            $sessionContainer = $container->get('DatosSession');
        } catch (\Exception $e) {
            session_unset();
            $sessionContainer = $container->get('DatosSession');
        }
        $controller = $event->getTarget();
        $controllerName = $event->getRouteMatch()->getParam('controller', null);
        $controllerName = explode("\\", $controllerName);
        $actionName = $event->getRouteMatch()->getParam('action', null);
        $controller_excluded = [
            'AccesoController' => [
                'logout',
                'login'
            ]
        ];
        if (array_key_exists($controllerName[2], $controller_excluded)) {
            if (in_array($actionName, $controller_excluded[$controllerName[2]])) {
                return;
            }
        }
        if (! $sessionContainer->access) {
            if ($controllerName[2] == 'IndexController' && $actionName == 'index') {
                return;
            }
            return $controller->redirect()->toUrl('/');
        } else {
            if ($controllerName[2] == 'IndexController' && $actionName == 'index') {
                return $controller->redirect()->toUrl('/panel');
            }
        }
        $this->InterfacePanel($event);
    }

    public function onDispatchError(MvcEvent $event)
    {
        $this->InterfacePanel($event);
    }

    private function InterfacePanel($event)
    {
        if (! isset($_SERVER['REQUEST_URI'])) {
            return;
        }
        $uri_parts = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
        $application = $event->getParam('application');
        $viewModel = $application->getMvcEvent()->getViewModel();
        $container = $event->getApplication()->getserviceManager();
        $sessionContainer = $container->get('DatosSession');
        $objAcceso = $container->get(Model\AccesoTable::class);
        $datosMenu = $objAcceso->obtenerMenuUsuario($sessionContainer->datosUsuario, $uri_parts);
        $viewModel->datosMenu = $datosMenu[0];
        $viewModel->breadCums = $datosMenu[1];
        $viewModel->datosUsuario = $sessionContainer->datosUsuario;
        if (file_exists(getcwd() . '/public/img/avatars/' . $sessionContainer->datosUsuario['token'] . '.jpg')) {
            $viewModel->image_perfil_url = '/img/avatars/' . $sessionContainer->datosUsuario['token'] . '.jpg';
        } else {
            $viewModel->image_perfil_url = '/img/avatars/male.png';
        }
    }
}
