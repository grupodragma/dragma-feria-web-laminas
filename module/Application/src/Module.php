<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

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
use Application\Service\MailSender;
use Application\Services;
use Application\Helper\Tools;

class TranslateClient {

    function translate($text) {
        return ['text'=> $text];
    }

}

class Module implements ConfigProviderInterface
{

    public function getConfig() {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function onBootstrap($e) {
        $application = $e->getParam('application');
        $services = $application->getServiceManager();
        $config = $services->get('Config');
        $viewModel = $application->getMvcEvent()->getViewModel();
        $e->getApplication()->getEventManager()->attach(MvcEvent::EVENT_ROUTE,
            function($event) use ($viewModel, $config) {
                $RouteMatch = $event->getRouteMatch();
                $idioma = $RouteMatch->getParam('lang', 'es');
                $viewModel->idiomaSeleccionado = $idioma;
                //Detected Language
                $viewModel->language = new TranslateClient();
            }
        );
        if(isset($_SERVER['HTTP_HOST'])){
            $protocol = (!empty($_SERVER['HTTPS'])) ? 'https' : 'http';
            $viewModel->base_url = $protocol.'://'.$_SERVER['HTTP_HOST'];
            $viewModel->url_backend = $config['url_backend'];
            $viewModel->node_server = $config['node_server'];
        }
        $eventManager = $e->getApplication()->getEventManager();
        $sharedEventManager = $eventManager->getSharedManager();
        $sharedEventManager->attach(AbstractActionController::class, MvcEvent::EVENT_DISPATCH, [$this, 'onDispatch'], 100);
        $sharedEventManager->attach('Laminas\Mvc\Application', MvcEvent::EVENT_DISPATCH_ERROR, [$this, 'onDispatchError'], 100);
    }

    public function getServiceConfig() {
        return [
            'factories' => [
                Model\FeriasTable::class => function($container){
                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('ferias', $adapter);
                    $table = new Model\FeriasTable($tableGateway,$adapter);
                    return $table;
                },
                Model\PaginasFeriasTable::class => function($container){
                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('paginas_ferias', $adapter);
                    $table = new Model\PaginasFeriasTable($tableGateway,$adapter);
                    return $table;
                },
                Model\PaginasTable::class => function($container){
                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('paginas', $adapter);
                    $table = new Model\PaginasTable($tableGateway,$adapter);
                    return $table;
                },
                Model\ClientesTable::class => function($container){
                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('clientes', $adapter);
                    $table = new Model\ClientesTable($tableGateway,$adapter);
                    return $table;
                },
                Model\VisitantesTable::class => function($container){
                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('visitantes', $adapter);
                    $table = new Model\VisitantesTable($tableGateway,$adapter);
                    return $table;
                },
                Model\PlanesTable::class => function($container){
                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('planes', $adapter);
                    $table = new Model\PlanesTable($tableGateway,$adapter);
                    return $table;
                },
                Model\MenusTable::class => function($container){
                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('menus', $adapter);
                    $table = new Model\MenusTable($tableGateway,$adapter);
                    return $table;
                },
                Model\CronogramasTable::class => function($container){
                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('cronogramas', $adapter);
                    $table = new Model\CronogramasTable($tableGateway,$adapter);
                    return $table;
                },
                Model\ZonasTable::class => function($container){
                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('zonas', $adapter);
                    $table = new Model\ZonasTable($tableGateway,$adapter);
                    return $table;
                },
                Model\EmpresasTable::class => function($container){
                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('empresas', $adapter);
                    $table = new Model\EmpresasTable($tableGateway,$adapter);
                    return $table;
                },
                Model\ProductosTable::class => function($container){
                    $adapter = $container->get(AdapterInterface::class);
                    $tableGatewayProductosImagenes = $container->get(Model\ProductosImagenesTable::class);
                    $tableGateway = new TableGateway('productos', $adapter);
                    $table = new Model\ProductosTable($tableGateway,$tableGatewayProductosImagenes);
                    return $table;
                },
                Model\PaginasStandTable::class => function($container){
                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('paginas_stand', $adapter);
                    $table = new Model\PaginasStandTable($tableGateway,$adapter);
                    return $table;
                },
                Model\OfertasTable::class => function($container){
                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('ofertas', $adapter);
                    $table = new Model\OfertasTable($tableGateway,$adapter);
                    return $table;
                },
                Model\ExpositoresTable::class => function($container){
                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('expositores', $adapter);
                    $table = new Model\ExpositoresTable($tableGateway,$adapter);
                    return $table;
                },
                Model\ExpositoresProductosTable::class => function($container){
                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('expositores_productos', $adapter);
                    $table = new Model\ExpositoresProductosTable($tableGateway,$adapter);
                    return $table;
                },
                Model\PaginasZonasTable::class => function($container){
                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('paginas_zonas', $adapter);
                    $table = new Model\PaginasZonasTable($tableGateway,$adapter);
                    return $table;
                },
                Model\StandGaleriaTable::class => function($container){
                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('stand_galeria', $adapter);
                    $table = new Model\StandGaleriaTable($tableGateway,$adapter);
                    return $table;
                },
                Model\StandTable::class => function($container){
                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('stand', $adapter);
                    $table = new Model\StandTable($tableGateway,$adapter);
                    return $table;
                },
                Model\PromocionesTable::class => function($container){
                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('promociones', $adapter);
                    $table = new Model\PromocionesTable($tableGateway,$adapter);
                    return $table;
                },
                Model\ExpositoresTarjetasTable::class => function($container){
                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('expositores_tarjetas', $adapter);
                    $table = new Model\ExpositoresTarjetasTable($tableGateway,$adapter);
                    return $table;
                },
                Model\ConferenciasTable::class => function($container){
                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('conferencias', $adapter);
                    $table = new Model\ConferenciasTable($tableGateway,$adapter);
                    return $table;
                },
                Model\UsuarioEventosTable::class => function($container){
                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('usuario_eventos', $adapter);
                    $table = new Model\UsuarioEventosTable($tableGateway,$adapter);
                    return $table;
                },
                Model\BuscadorTable::class => function($container){
                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('buscador', $adapter);
                    $table = new Model\BuscadorTable($tableGateway,$adapter);
                    return $table;
                },
                Model\BancosTable::class => function($container){
                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('bancos', $adapter);
                    $table = new Model\BancosTable($tableGateway,$adapter);
                    return $table;
                },
                Model\ProductosImagenesTable::class => function($container){
                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('productos_imagenes', $adapter);
                    $table = new Model\ProductosImagenesTable($tableGateway,$adapter);
                    return $table;
                },
                Model\PlanosTable::class => function($container){
                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('planos', $adapter);
                    $table = new Model\PlanosTable($tableGateway,$adapter);
                    return $table;
                },
                Model\SeoTable::class => function($container){
                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('seo', $adapter);
                    return new Model\SeoTable($tableGateway,$adapter);
                },
                Model\PaginasBotonesTable::class => function($container){
                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('paginas_botones', $adapter);
                    return new Model\PaginasBotonesTable($tableGateway,$adapter);
                },
                Model\FeriasCorreosTable::class => function($container){
                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('ferias_correos', $adapter);
                    return new Model\FeriasCorreosTable($tableGateway,$adapter);
                },
                Model\AgendaVirtualTable::class => function($container){
                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('agenda_virtual', $adapter);
                    return new Model\AgendaVirtualTable($tableGateway,$adapter);
                },
                Model\ChatsGeneralTable::class => function($container){
                    $tableGatewayExpositores = $container->get(Model\ExpositoresTable::class);
                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('chats_conferencias', $adapter);
                    return new Model\ChatsGeneralTable($tableGateway,$tableGatewayExpositores);
                },
                Model\FeriasPromocionesTable::class => function($container){
                    $tableGatewayExpositores = $container->get(Model\ExpositoresTable::class);
                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('ferias_promociones', $adapter);
                    return new Model\FeriasPromocionesTable($tableGateway,$tableGatewayExpositores);
                },
                Model\VisitantesRegistrosTable::class => function($container){
                    $tableGatewayExpositores = $container->get(Model\ExpositoresTable::class);
                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('visitantes_registros', $adapter);
                    return new Model\VisitantesRegistrosTable($tableGateway,$tableGatewayExpositores);
                },
                Model\UsuarioTable::class => function($container){
                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('fd_usuarios', $adapter);
                    return new Model\UsuarioTable($tableGateway, $adapter);
                },
                Model\RegistroEnvioCorreosTable::class => function($container){
                    $adapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('registro_envio_correos', $adapter);
                    return new Model\RegistroEnvioCorreosTable($tableGateway, $adapter);
                },
                \Laminas\Authentication\AuthenticationService::class => function($container) {
                    $sessionManager = $container->get(\Laminas\Session\SessionManager::class);
                    $authStorage = new SessionStorage('Zend_Auth', 'session', $sessionManager);
                    $authAdapter = $container->get(Service\AuthAdapter::class);
                    return new AuthenticationService($authStorage, $authAdapter);
                },
                Service\AuthAdapter::class => function($container) {
                    $sessionContainer = $container->get('DatosSession');
                    return new Service\AuthAdapter(
                        $container->get(Model\VisitantesTable::class),
                        $sessionContainer,
                        $container->get(Model\ExpositoresTable::class),
                        $container->get(Model\UsuarioEventosTable::class)
                    );
                },
                Service\AuthManager::class => function($container) {
                    $authenticationService = $container->get(\Laminas\Authentication\AuthenticationService::class);
                    $sessionManager = $container->get(SessionManager::class);
                    return new AuthManager($authenticationService, $sessionManager);
                },
                Service\MailSender::class => function($container) {
                    return new MailSender($container);
                },
                Helper\Tools::class => function($container) {
                    return new Tools($container);
                }
            ],
        ];
    }

    public function getControllerConfig() {
        return [
            'factories' => [
                Controller\IndexController::class => function($container) {
                    return new Controller\IndexController(
                        $container,
                        $container->get(Model\FeriasTable::class),
                        $container->get(Model\PaginasFeriasTable::class),
                        $container->get(Model\PaginasTable::class),
                        $container->get(Model\VisitantesTable::class),
                        $container->get(Model\SeoTable::class)
                    );
                },
                Controller\AccesoController::class => function($container) {
                    return new Controller\AccesoController(
                        $container,
                        $container->get(Service\AuthManager::class),
                        $container->get(Model\VisitantesTable::class),
                        $container->get(Model\FeriasTable::class),
                        $container->get(Service\MailSender::class),
                        $container->get(Model\ExpositoresTable::class),
                        $container->get(Model\FeriasCorreosTable::class),
                        $container->get(Model\VisitantesRegistrosTable::class),
                        $container->get(Model\UsuarioTable::class)
                    );
                },
                Controller\PanelController::class => function($container) {
                    return new Controller\PanelController(
                        $container,
                        $container->get(Model\CronogramasTable::class),
                        $container->get(Model\PaginasFeriasTable::class),
                        $container->get(Model\PaginasTable::class),
                        $container->get(Model\ZonasTable::class),
                        $container->get(Model\EmpresasTable::class),
                        $container->get(Model\ProductosTable::class),
                        $container->get(Model\PaginasStandTable::class),
                        $container->get(Model\OfertasTable::class),
                        $container->get(Model\ExpositoresTable::class),
                        $container->get(Model\ExpositoresProductosTable::class),
                        $container->get(Model\PaginasZonasTable::class),
                        $container->get(Model\StandGaleriaTable::class),
                        $container->get(Model\StandTable::class),
                        $container->get(Model\PromocionesTable::class),
                        $container->get(Model\ExpositoresTarjetasTable::class),
                        $container->get(Model\VisitantesTable::class),
                        $container->get(Model\ConferenciasTable::class),
                        $container->get(Model\UsuarioEventosTable::class),
                        $container->get(Service\MailSender::class),
                        $container->get(Model\BuscadorTable::class),
                        $container->get(Model\BancosTable::class),
                        $container->get(Model\ProductosImagenesTable::class),
                        $container->get(Model\PlanosTable::class),
                        $container->get(Model\SeoTable::class),
                        $container->get(Model\PaginasBotonesTable::class),
                        $container->get(Helper\Tools::class),
                        $container->get(Model\FeriasCorreosTable::class),
                        $container->get(Model\AgendaVirtualTable::class),
                        $container->get(Model\ChatsGeneralTable::class),
                        $container->get(Model\FeriasPromocionesTable::class),
                        $container->get(Model\RegistroEnvioCorreosTable::class)
                    );
                },
                Controller\ClienteController::class => function($container) {
                    return new Controller\ClienteController(
                        $container,
                        $container->get(Model\VisitantesTable::class),
                        $container->get(Model\FeriasTable::class),
                        $container->get(Model\ExpositoresTable::class),
                        $container->get(Model\AgendaVirtualTable::class),
                        $container->get(Service\MailSender::class),
                        $container->get(Model\FeriasCorreosTable::class)
                    );
                }
            ],
        ];
    }

    public function onDispatch(MvcEvent $event) {
        $this->InterfacePanel($event);
    }

    public function onDispatchError(MvcEvent $event){
        $this->InterfacePanel($event);
    }

    private function InterfacePanel($event){
        $partesUrl = explode('/',trim($_SERVER['REQUEST_URI'],'/'));
        $application = $event->getParam('application');
        $viewModel = $application->getMvcEvent()->getViewModel();
        $container = $event->getApplication()->getserviceManager();
        $sessionContainer = $container->get('DatosSession');
        $config = $container->get('Config');
        $dominio = $this->obtenerDominio();
        if($dominio == '')die('La plataforma no esta disponible');
        $viewModel->dominio = $dominio;
        $objFeriasTable = $container->get(Model\FeriasTable::class);
        $objClientesTable = $container->get(Model\ClientesTable::class);
        $objMenusTable = $container->get(Model\MenusTable::class);
        $objExpositoresTable = $container->get(Model\ExpositoresTable::class);
        $dataFeria = $objFeriasTable->obtenerDatoFerias(['dominio'=> $dominio]);
        if(!$dataFeria)die('La plataforma no esta disponible');
        $viewModel->menuSecuencia = $objFeriasTable->obtenerPasoSecuencia($dataFeria['idferias']);
        $dataCliente = $objClientesTable->obtenerDatoClientes(['idclientes'=> $dataFeria['idclientes']]);
        $viewModel->feria = $dataFeria;
        $viewModel->feria['config_formulario'] = ( $viewModel->feria['config_formulario'] != '' ) ? json_decode($viewModel->feria['config_formulario'], true) : json_decode('[{"requerido":1,"nombre":"Nombres","keyinput":"nombres","elemento":"input","opcion":[]},{"requerido":1,"nombre":"Apellido Paterno","keyinput":"apellido_paterno","elemento":"input","opcion":[]},{"requerido":1,"nombre":"Apellido Materno","keyinput":"apellido_materno","elemento":"input","opcion":[]},{"requerido":1,"nombre":"Correo","keyinput":"correo","elemento":"input","opcion":[]},{"requerido":1,"nombre":"Teléfono","keyinput":"telefono","elemento":"input","opcion":[]}]', true);
        $feriaColorConfiguracionInicial = $config['feria_colores_inicial'];
        $viewModel->feria_color_personalizado = ( $dataFeria && $dataFeria['color_personalizado'] != null ) ? json_decode($dataFeria['color_personalizado'], true) : $feriaColorConfiguracionInicial;
        $viewModel->cliente = $dataCliente;
        $viewModel->promociones = $container->get(Model\FeriasPromocionesTable::class)->obtenerFechaProgramadaHoy($dataFeria['idferias']);
        $viewModel->datosUsuario = $sessionContainer->datosUsuario;
        $viewModel->datosUsuarioLogin = $this->obtenerDatosUsuarioLogin($sessionContainer);
        if($viewModel->datosUsuarioLogin){
            $viewModel->datosExpositor = ( $viewModel->datosUsuarioLogin['tipo'] == "E") ? $objExpositoresTable->obtenerExpositorEmpresa($dataFeria['idferias'], $viewModel->datosUsuarioLogin['idusuario']) : [];
        }
        $viewModel->menus = [
            'encabezado'=> $objMenusTable->obtenerTipoMenusPorPlanes($dataFeria['idplanes'], 'E'),
            'piePagina'=> $objMenusTable->obtenerTipoMenusPorPlanes($dataFeria['idplanes'], 'PP'),
            'hash_url_activo'=> (count($partesUrl) == 2) ? $partesUrl[1] : ''
        ];
        $viewModel->listaIdiomas = [
            'es'=> 'Español',
            'en'=> 'Inglés',
            'pt'=> 'Portugués',
            'zh'=> 'Chino'
        ];
        $viewModel->urlActual = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $viewModel->configuracion = $config;
        //Open Graph
        $viewModel->openGraph = [
            'url_imagen'=> ( $dataFeria['config_fondo_correo_hash_imagen'] != "" ) ? "ferias/correos/fondos/".$dataFeria['config_fondo_correo_hash_imagen'] : "ferias/logo/".$dataFeria['hash_logo']
        ];
    }

    private function obtenerDatosUsuarioLogin($sessionContainer){
        $usuario = [];
        if($dataUsuario = $sessionContainer->datosUsuario){
            $usuario = [
                'tipo'=> $dataUsuario['tipo'],
                'idusuario'=> $this->obtenerIdUsuarioSesion($sessionContainer->datosUsuario),
                'nombres'=> $dataUsuario['nombres'],
                'apellido_paterno'=> $dataUsuario['apellido_paterno'],
                'apellido_materno'=> $dataUsuario['apellido_materno'],
                'cargo'=> (isset($dataUsuario['cargo'])) ? $dataUsuario['cargo'] : '',
                'empresa'=> (isset($dataUsuario['empresa'])) ? $dataUsuario['empresa'] : '',
                'correo'=> $dataUsuario['correo'],
                'telefono'=> $dataUsuario['telefono']
            ];
        }
        return $usuario;
    }

    private function obtenerIdUsuarioSesion($sesionUsuario){
        if(!$sesionUsuario)return 0;
        if(isset($sesionUsuario['idvisitantes'])){
            return (int)$sesionUsuario['idvisitantes'];
        } else if (isset($sesionUsuario['idexpositores'])) {
            return (int)$sesionUsuario['idexpositores'];
        } else {
            return (int)$sesionUsuario['idspeakers'];
        }
    }

    private function obtenerDominio() {
        $dominio = '';
        $partesDominio = explode(".",$_SERVER['SERVER_NAME']);
        switch(count($partesDominio)){
            case 3:
            case 1: //localhost
                $dominio = str_replace("www.", "", $_SERVER['SERVER_NAME']);
            break;
            case 4:
            case 2: // dominio
                $dominio = str_replace("www.", "", $_SERVER['SERVER_NAME']);
            break;
            default:
                $dominio = '';
            break;
        }
        return $dominio;
    }

}
