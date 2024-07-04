<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\View\Model\JsonModel;

class PanelController extends AbstractActionController {

    protected $serviceManager;
    protected $objCronogramasTable;
    protected $objPaginasFeriasTable;
    protected $objPaginasTable;
    protected $objZonasTable;
    protected $objEmpresasTable;
    protected $objProductosTable;
    protected $objPaginasStandTable;
    protected $objOfertasTable;
    protected $objExpositoresTable;
    protected $objExpositoresProductosTable;
    protected $objPaginasZonasTable;
    protected $objStandGaleriaTable;
    protected $objStandTable;
    protected $objPromocionesTable;
    protected $objExpositoresTarjetasTable;
    protected $objVisitantesTable;
    protected $objConferenciasTable;
    protected $objUsuarioEventosTable;
    protected $objMailSender;
    protected $objBuscadorTable;
    protected $objBancosTable;
    protected $objProductosImagenesTable;
    protected $objPlanosTable;
    protected $objSeoTable;
    protected $objPaginasBotonesTable;
    protected $objTools;
    protected $objFeriasCorreosTable;
    protected $objAgendaVirtualTable;
    protected $objChatsGeneralTable;
    protected $objFeriasPromocionesTable;
    protected $objRegistroEnvioCorreosTable;

    public function __construct($serviceManager, $objCronogramasTable, $objPaginasFeriasTable, $objPaginasTable, $objZonasTable, $objEmpresasTable, $objProductosTable, $objPaginasStandTable, $objOfertasTable, $objExpositoresTable, $objExpositoresProductosTable, $objPaginasZonasTable, $objStandGaleriaTable, $objStandTable, $objPromocionesTable, $objExpositoresTarjetasTable, $objVisitantesTable, $objConferenciasTable, $objUsuarioEventosTable, $objMailSender, $objBuscadorTable, $objBancosTable, $objProductosImagenesTable, $objPlanosTable, $objSeoTable, $objPaginasBotonesTable,$objTools, $objFeriasCorreosTable, $objAgendaVirtualTable, $objChatsGeneralTable, $objFeriasPromocionesTable, $objRegistroEnvioCorreosTable) {
        $this->serviceManager = $serviceManager;
        $this->objCronogramasTable = $objCronogramasTable;
        $this->objPaginasFeriasTable = $objPaginasFeriasTable;
        $this->objPaginasTable = $objPaginasTable;
        $this->objZonasTable = $objZonasTable;
        $this->objEmpresasTable = $objEmpresasTable;
        $this->objProductosTable = $objProductosTable;
        $this->objPaginasStandTable = $objPaginasStandTable;
        $this->objOfertasTable = $objOfertasTable;
        $this->objExpositoresTable = $objExpositoresTable;
        $this->objExpositoresProductosTable = $objExpositoresProductosTable;
        $this->objPaginasZonasTable = $objPaginasZonasTable;
        $this->objStandGaleriaTable = $objStandGaleriaTable;
        $this->objStandTable = $objStandTable;
        $this->objPromocionesTable = $objPromocionesTable;
        $this->objExpositoresTarjetasTable = $objExpositoresTarjetasTable;
        $this->objVisitantesTable = $objVisitantesTable;
        $this->objConferenciasTable = $objConferenciasTable;
        $this->objUsuarioEventosTable = $objUsuarioEventosTable;
        $this->objMailSender = $objMailSender;
        $this->sessionContainer = $this->serviceManager->get('DatosSession')->datosUsuario;
        $this->objBuscadorTable = $objBuscadorTable;
        $this->objBancosTable = $objBancosTable;
        $this->objProductosImagenesTable = $objProductosImagenesTable;
        $this->objPlanosTable = $objPlanosTable;
        $this->objSeoTable = $objSeoTable;
        $this->objPaginasBotonesTable = $objPaginasBotonesTable;
        $this->objTools = $objTools;
        $this->objFeriasCorreosTable = $objFeriasCorreosTable;
        $this->objAgendaVirtualTable = $objAgendaVirtualTable;
        $this->objChatsGeneralTable = $objChatsGeneralTable;
        $this->objFeriasPromocionesTable = $objFeriasPromocionesTable;
        $this->objRegistroEnvioCorreosTable = $objRegistroEnvioCorreosTable;
    }

    private function validarSecuenciaPagina($accion){
        $secuencia = $this->layout()->menuSecuencia;
        if( !$secuencia ) {
            die('>>> Plataforma no disponible');
        }
        switch($accion) {
            case 'registro':
            //
            break;
            case 'contenido':
                if( ( !$this->sessionContainer && (int)$secuencia['home']['estado'] && (int)$secuencia['registro']['estado'] ) || ( !$this->sessionContainer && (int)$secuencia['registro']['estado'] )) {
                    return $this->redirect()->toUrl('/'.$this->layout()->idiomaSeleccionado);
                }
            break;
            default:
            break;
        }
    }
    
    public function indexAction() {
        $this->validarSecuenciaPagina('contenido');
        $this->layout()->setTemplate('layout/panel');
        return new ViewModel();
    }

    public function recepcionAction() {
        $this->validarSecuenciaPagina('contenido');
        $this->layout()->setTemplate('layout/panel');
        $dataPaginaConfiguracion = $this->obtenerPaginaConfiguracion('recepcion');
        $dataPaginaConfiguracion['seo'] = $this->objSeoTable->obtenerSeoFeria($this->layout()->feria['idferias'], 1); //ID 1 RECEPCIÓN
        $dataPaginasBotones = $this->objPaginasBotonesTable->obtenerDatoPaginasBotones(['idferias'=> $this->layout()->feria['idferias'], 'idpaginas'=> 12]); //ID 12 Recepción
        $dataPaginaConfiguracion['botones'] = ($dataPaginasBotones) ? json_decode($dataPaginasBotones['configuracion'], true) : [];
        $dataPaginaConfiguracion['fondo_video'] = $this->obtenerFondoVideo($dataPaginaConfiguracion['configuracion']);
        return new ViewModel($dataPaginaConfiguracion);
    }

    public function conferenciasAction() {
        $this->validarSecuenciaPagina('contenido');
        $this->layout()->setTemplate('layout/panel');
        $dataPaginaConfiguracion = $this->obtenerPaginaConfiguracion('hall-conferencias');
        $dataPaginaConfiguracion['seo'] = $this->objSeoTable->obtenerSeoFeria($this->layout()->feria['idferias'], 2); //ID 2 CONFERENCIAS
        $dataPaginasBotones = $this->objPaginasBotonesTable->obtenerDatoPaginasBotones(['idferias'=> $this->layout()->feria['idferias'], 'idpaginas'=> 9]); //ID 9 Conferencias
        $dataPaginaConfiguracion['botones'] = ($dataPaginasBotones) ? json_decode($dataPaginasBotones['configuracion'], true) : [];
        $dataPaginaConfiguracion['fondo_video'] = $this->obtenerFondoVideo($dataPaginaConfiguracion['configuracion']);
        return new ViewModel($dataPaginaConfiguracion);
    }

    public function ruedaNegociosAction() {
        $this->validarSecuenciaPagina('contenido');
        $this->layout()->setTemplate('layout/panel');
        return new ViewModel();
    }

    public function expositoresVivoAction() {
        $this->validarSecuenciaPagina('contenido');
        $this->layout()->setTemplate('layout/panel');
        $zona = $this->params()->fromRoute('zona', '');
        $ordenzona = $this->params()->fromRoute('ordenzona', 0);
        $empresa = $this->params()->fromRoute('empresa', '');
        $ordenempresa = $this->params()->fromRoute('ordenempresa', 0);
        $hashurlempresa = $this->params()->fromRoute('hashurlempresa', '');
        if($zona == '' || $ordenzona === 0 || $empresa == '' || $ordenempresa === 0 || $hashurlempresa === '')return $this->redirect()->toRoute('home');
        $dataZonas = $this->objZonasTable->obtenerDatoZonas(['idferias'=> $this->layout()->feria['idferias'], 'orden'=> $ordenzona]);
        $totalZonas = count($this->objZonasTable->obtenerZonasPorFeria($this->layout()->feria['idferias']));
        if(!$dataZonas)return $this->redirect()->toRoute('home');
        // ZONA SIGUIENTE ANTERIOR EMPRESA [INICIO] //
        $dataEmpresaZonaSiguiente = $this->objEmpresasTable->obtenerEmpresaZonaInicial($this->layout()->feria['idferias'], $ordenzona + 1);
        $dataEmpresaZonaAnterior = $this->objEmpresasTable->obtenerEmpresaZonaInicial($this->layout()->feria['idferias'], $ordenzona - 1);
        // ZONA SIGUIENTE EMPRESA [FIN] //
        $dataEmpresa = $this->objEmpresasTable->obtenerDatoEmpresas(['idzonas'=> $dataZonas['idzonas'], 'orden'=> $ordenempresa, 'hash_url'=> $hashurlempresa]);
        if(!$dataEmpresa)return $this->redirect()->toRoute('home');
        $dataEmpresaSiguiente = $this->objEmpresasTable->obtenerDatoEmpresas(['idzonas'=> $dataZonas['idzonas'], 'orden'=> ($ordenempresa + 1)]);
        $dataEmpresaAnterior = $this->objEmpresasTable->obtenerDatoEmpresas(['idzonas'=> $dataZonas['idzonas'], 'orden'=> ($ordenempresa - 1)]);
        $totalEmpresas = count($this->objEmpresasTable->obtenerDatosEmpresas($this->layout()->feria['idferias'], $dataZonas['idzonas']));
        $dataEmpresas = $this->objEmpresasTable->obtenerDatoCondicionEmpresas($dataZonas['idzonas']);
        $dataExpositor = $this->objExpositoresTable->obtenerDatoExpositores(['idexpositores'=> $dataEmpresa['idexpositores']]);
        $dataExpositoresProductos = $this->objExpositoresProductosTable->obtenerExpositoresProductos($dataEmpresa['idexpositores']);
        $dataProductos = $this->objProductosTable->obtenerDatosProductos(['idempresas'=> $dataEmpresa['idempresas']]);
        $dataPromociones = $this->objPromocionesTable->obtenerDatosPromociones(['idempresas'=> $dataEmpresa['idempresas']]);
        $dataPlanos = $this->objPlanosTable->obtenerDatosPlanos(['idempresas'=> $dataEmpresa['idempresas']]);
        $data = [
            'ordenzona'=> $ordenzona,
            'zona'=> $dataZonas,
            'ordenempresa'=> $ordenempresa,
            'empresa'=> $dataEmpresa,
            'empresaSiguiente'=> $dataEmpresaSiguiente,
            'empresaAnterior'=> $dataEmpresaAnterior,
            'zonaEmpresaSiguiente'=> $dataEmpresaZonaSiguiente,
            'zonaEmpresaAnterior'=> $dataEmpresaZonaAnterior,
            'empresas'=> $dataEmpresas,
            'totalZonas'=> $totalZonas,
            'totalEmpresas'=> $totalEmpresas,
            'expositor'=> $dataExpositor,
            'productos'=> $dataExpositoresProductos,
            'totalProductos'=> count($dataProductos),
            'totalPromociones'=> count($dataPromociones),
            'totalPlanos'=> count($dataPlanos),
            'seo'=> $this->objSeoTable->obtenerSeoFeria($this->layout()->feria['idferias'], 24), //ID 24 EXPOSITORES VIVO
            'estadoAgendaVirtualUsuario'=> $this->validarOpcionAtencionVirtualUsuario($dataEmpresa['idempresas'])
        ];
        return new ViewModel($data);
    }

    private function validarOpcionAtencionVirtualUsuario($idempresas) {
        $estadoOpcion = false;
        if($this->sessionContainer) {
            $idUsuarioSesion = $this->obtenerIdUsuarioSesion();
            $fecha = date("Y-m-d H:i");

            if( (int)$idempresas > 0 ) {
                $agendaUsuarioReservado = $this->objAgendaVirtualTable->obtenerAgendaUsuarioPorEmpresa($this->layout()->feria['idferias'], $idUsuarioSesion, $this->layout()->datosUsuario['tipo'], $fecha, $idempresas);
            } else {
                $agendaUsuarioReservado = $this->objAgendaVirtualTable->obtenerAgendaUsuario($this->layout()->feria['idferias'], $idUsuarioSesion, $this->layout()->datosUsuario['tipo'], $fecha);
            }

            if(!empty($agendaUsuarioReservado)){
                $estadoOpcion = true;
            }
        }
        return $estadoOpcion;
    }

    private function obtenerIdUsuarioSesion(){
        if(!$this->sessionContainer)return 0;
        if(isset($this->sessionContainer['idvisitantes'])){
            return (int)$this->sessionContainer['idvisitantes'];
        } else if (isset($this->sessionContainer['idexpositores'])) {
            return (int)$this->sessionContainer['idexpositores'];
        } else {
            return (int)$this->sessionContainer['idspeakers'];
        }
    }

    public function ofertaDemandaAction() {
        $this->validarSecuenciaPagina('contenido');
        $this->layout()->setTemplate('layout/panel');
        return new ViewModel();
    }

    public function realidadVirtualAction() {
        $this->validarSecuenciaPagina('contenido');
        $this->layout()->setTemplate('layout/panel');
        $idempresa = $this->params()->fromRoute('idempresa', 0);
        $dataEmpresa = $this->objEmpresasTable->obtenerDatoEmpresas(['idempresas'=> $idempresa]);
        if(!$dataEmpresa)$dataEmpresa=[];
        return new ViewModel($dataEmpresa);
    }

    public function productosAction() {
        $this->validarSecuenciaPagina('contenido');
        $this->layout()->setTemplate('layout/panel');
        $zona = $this->params()->fromRoute('zona', '');
        $ordenzona = $this->params()->fromRoute('ordenzona', 0);
        $empresa = $this->params()->fromRoute('empresa', '');
        $ordenempresa = $this->params()->fromRoute('ordenempresa', 0);
        $hashurlempresa = $this->params()->fromRoute('hashurlempresa', '');
        if($zona == '' || $ordenzona === 0 || $empresa == '' || $ordenempresa === 0 || $hashurlempresa === '')return $this->redirect()->toRoute('home');
        $dataZonas = $this->objZonasTable->obtenerDatoZonas(['idferias'=> $this->layout()->feria['idferias'], 'orden'=> $ordenzona]);
        $totalZonas = count($this->objZonasTable->obtenerZonasPorFeria($this->layout()->feria['idferias']));
        if(!$dataZonas)return $this->redirect()->toRoute('home');
        // ZONA SIGUIENTE ANTERIOR EMPRESA [INICIO] //
        $dataEmpresaZonaSiguiente = $this->objEmpresasTable->obtenerEmpresaZonaInicial($this->layout()->feria['idferias'], $ordenzona + 1);
        $dataEmpresaZonaAnterior = $this->objEmpresasTable->obtenerEmpresaZonaInicial($this->layout()->feria['idferias'], $ordenzona - 1);
        // ZONA SIGUIENTE EMPRESA [FIN] //
        $dataEmpresa = $this->objEmpresasTable->obtenerDatoEmpresas(['idzonas'=> $dataZonas['idzonas'], 'orden'=> $ordenempresa, 'hash_url'=> $hashurlempresa]);
        if(!$dataEmpresa)return $this->redirect()->toRoute('home');
        $dataEmpresaSiguiente = $this->objEmpresasTable->obtenerDatoEmpresas(['idzonas'=> $dataZonas['idzonas'], 'orden'=> ($ordenempresa + 1)]);
        $dataEmpresaAnterior = $this->objEmpresasTable->obtenerDatoEmpresas(['idzonas'=> $dataZonas['idzonas'], 'orden'=> ($ordenempresa - 1)]);
        $totalEmpresas = count($this->objEmpresasTable->obtenerDatosEmpresas($this->layout()->feria['idferias'], $dataZonas['idzonas']));
        $dataEmpresas = $this->objEmpresasTable->obtenerDatoCondicionEmpresas($dataZonas['idzonas']);
        $dataExpositor = $this->objExpositoresTable->obtenerDatoExpositores(['idexpositores'=> $dataEmpresa['idexpositores']]);
        $dataProductos = $this->objProductosTable->obtenerDatosProductosImagenes(['idempresas'=> $dataEmpresa['idempresas']]);
        $dataPromociones = $this->objPromocionesTable->obtenerDatosPromociones(['idempresas'=> $dataEmpresa['idempresas']]);
        $dataPlanos = $this->objPlanosTable->obtenerDatosPlanos(['idempresas'=> $dataEmpresa['idempresas']]);
        if(!count($dataProductos))return $this->redirect()->toRoute('home');
        $data = [
            'ordenzona'=> $ordenzona,
            'zona'=> $dataZonas,
            'ordenempresa'=> $ordenempresa,
            'empresa'=> $dataEmpresa,
            'empresaSiguiente'=> $dataEmpresaSiguiente,
            'empresaAnterior'=> $dataEmpresaAnterior,
            'zonaEmpresaSiguiente'=> $dataEmpresaZonaSiguiente,
            'zonaEmpresaAnterior'=> $dataEmpresaZonaAnterior,
            'empresas'=> $dataEmpresas,
            'totalZonas'=> $totalZonas,
            'totalEmpresas'=> $totalEmpresas,
            'expositor'=> $dataExpositor,
            'productos'=> $dataProductos,
            'totalProductos'=> count($dataProductos),
            'totalPromociones'=> count($dataPromociones),
            'totalPlanos'=> count($dataPlanos),
            'seo'=> $this->objSeoTable->obtenerSeoFeria($this->layout()->feria['idferias'], 7), //ID 7 PRODUCTOS
            'maestriaEnProducto'=> $this->maestriaEnProducto($dataProductos)
        ];
        return new ViewModel($data);
    }

    private function maestriaEnProducto($productos){
        $result = ['existe'=> false];
        if(!empty($productos)){
            foreach($productos as $item) {
                if($item['institucion_nombre'] != '') {
                    $result['existe'] = true;
                    $result['datos'] = $item; 
                }
            }
        }
        return $result;
    }

    public function planosAction() {
        $this->validarSecuenciaPagina('contenido');
        $this->layout()->setTemplate('layout/panel');
        $zona = $this->params()->fromRoute('zona', '');
        $ordenzona = $this->params()->fromRoute('ordenzona', 0);
        $empresa = $this->params()->fromRoute('empresa', '');
        $ordenempresa = $this->params()->fromRoute('ordenempresa', 0);
        $hashurlempresa = $this->params()->fromRoute('hashurlempresa', '');
        if($zona == '' || $ordenzona === 0 || $empresa == '' || $ordenempresa === 0 || $hashurlempresa === '')return $this->redirect()->toRoute('home');
        $dataZonas = $this->objZonasTable->obtenerDatoZonas(['idferias'=> $this->layout()->feria['idferias'], 'orden'=> $ordenzona]);
        $totalZonas = count($this->objZonasTable->obtenerZonasPorFeria($this->layout()->feria['idferias']));
        if(!$dataZonas)return $this->redirect()->toRoute('home');
        // ZONA SIGUIENTE ANTERIOR EMPRESA [INICIO] //
        $dataEmpresaZonaSiguiente = $this->objEmpresasTable->obtenerEmpresaZonaInicial($this->layout()->feria['idferias'], $ordenzona + 1);
        $dataEmpresaZonaAnterior = $this->objEmpresasTable->obtenerEmpresaZonaInicial($this->layout()->feria['idferias'], $ordenzona - 1);
        // ZONA SIGUIENTE EMPRESA [FIN] //
        $dataEmpresa = $this->objEmpresasTable->obtenerDatoEmpresas(['idzonas'=> $dataZonas['idzonas'], 'orden'=> $ordenempresa, 'hash_url'=> $hashurlempresa]);
        if(!$dataEmpresa)return $this->redirect()->toRoute('home');
        $dataEmpresaSiguiente = $this->objEmpresasTable->obtenerDatoEmpresas(['idzonas'=> $dataZonas['idzonas'], 'orden'=> ($ordenempresa + 1)]);
        $dataEmpresaAnterior = $this->objEmpresasTable->obtenerDatoEmpresas(['idzonas'=> $dataZonas['idzonas'], 'orden'=> ($ordenempresa - 1)]);
        $totalEmpresas = count($this->objEmpresasTable->obtenerDatosEmpresas($this->layout()->feria['idferias'], $dataZonas['idzonas']));
        $dataEmpresas = $this->objEmpresasTable->obtenerDatoCondicionEmpresas($dataZonas['idzonas']);
        $dataExpositor = $this->objExpositoresTable->obtenerDatoExpositores(['idexpositores'=> $dataEmpresa['idexpositores']]);
        $dataProductos = $this->objProductosTable->obtenerDatosProductos(['idempresas'=> $dataEmpresa['idempresas']]);
        $dataPromociones = $this->objPromocionesTable->obtenerDatosPromociones(['idempresas'=> $dataEmpresa['idempresas']]);
        $dataPlanos = $this->objPlanosTable->obtenerDatosPlanos(['idempresas'=> $dataEmpresa['idempresas']]);
        if(!count($dataPlanos))return $this->redirect()->toRoute('home');
        $data = [
            'ordenzona'=> $ordenzona,
            'zona'=> $dataZonas,
            'ordenempresa'=> $ordenempresa,
            'empresa'=> $dataEmpresa,
            'empresaSiguiente'=> $dataEmpresaSiguiente,
            'empresaAnterior'=> $dataEmpresaAnterior,
            'zonaEmpresaSiguiente'=> $dataEmpresaZonaSiguiente,
            'zonaEmpresaAnterior'=> $dataEmpresaZonaAnterior,
            'empresas'=> $dataEmpresas,
            'totalZonas'=> $totalZonas,
            'totalEmpresas'=> $totalEmpresas,
            'expositor'=> $dataExpositor,
            'productos'=> $dataProductos,
            'planos'=> $dataPlanos,
            'totalProductos'=> count($dataProductos),
            'totalPromociones'=> count($dataPromociones),
            'totalPlanos'=> count($dataPlanos),
            'seo'=> $this->objSeoTable->obtenerSeoFeria($this->layout()->feria['idferias'], 16) //ID 16 PLANOS
        ];
        return new ViewModel($data);
    }

    public function promocionesAction() {
        $this->validarSecuenciaPagina('contenido');
        $this->layout()->setTemplate('layout/panel');
        $zona = $this->params()->fromRoute('zona', '');
        $ordenzona = $this->params()->fromRoute('ordenzona', 0);
        $empresa = $this->params()->fromRoute('empresa', '');
        $ordenempresa = $this->params()->fromRoute('ordenempresa', 0);
        $hashurlempresa = $this->params()->fromRoute('hashurlempresa', '');
        if($zona == '' || $ordenzona === 0 || $empresa == '' || $ordenempresa === 0 || $hashurlempresa === '')return $this->redirect()->toRoute('home');
        $dataZonas = $this->objZonasTable->obtenerDatoZonas(['idferias'=> $this->layout()->feria['idferias'], 'orden'=> $ordenzona]);
        $totalZonas = count($this->objZonasTable->obtenerZonasPorFeria($this->layout()->feria['idferias']));
        if(!$dataZonas)return $this->redirect()->toRoute('home');
        // ZONA SIGUIENTE ANTERIOR EMPRESA [INICIO] //
        $dataEmpresaZonaSiguiente = $this->objEmpresasTable->obtenerEmpresaZonaInicial($this->layout()->feria['idferias'], $ordenzona + 1);
        $dataEmpresaZonaAnterior = $this->objEmpresasTable->obtenerEmpresaZonaInicial($this->layout()->feria['idferias'], $ordenzona - 1);
        // ZONA SIGUIENTE EMPRESA [FIN] //
        $dataEmpresa = $this->objEmpresasTable->obtenerDatoEmpresas(['idzonas'=> $dataZonas['idzonas'], 'orden'=> $ordenempresa, 'hash_url'=> $hashurlempresa]);
        if(!$dataEmpresa)return $this->redirect()->toRoute('home');
        $dataEmpresaSiguiente = $this->objEmpresasTable->obtenerDatoEmpresas(['idzonas'=> $dataZonas['idzonas'], 'orden'=> ($ordenempresa + 1)]);
        $dataEmpresaAnterior = $this->objEmpresasTable->obtenerDatoEmpresas(['idzonas'=> $dataZonas['idzonas'], 'orden'=> ($ordenempresa - 1)]);
        $totalEmpresas = count($this->objEmpresasTable->obtenerDatosEmpresas($this->layout()->feria['idferias'], $dataZonas['idzonas']));
        $dataEmpresas = $this->objEmpresasTable->obtenerDatoCondicionEmpresas($dataZonas['idzonas']);
        $dataExpositor = $this->objExpositoresTable->obtenerDatoExpositores(['idexpositores'=> $dataEmpresa['idexpositores']]);
        $dataProductos = $this->objProductosTable->obtenerDatosProductos(['idempresas'=> $dataEmpresa['idempresas']]);
        $dataPromociones = $this->objPromocionesTable->obtenerDatosPromociones(['idempresas'=> $dataEmpresa['idempresas']]);
        $dataPlanos = $this->objPlanosTable->obtenerDatosPlanos(['idempresas'=> $dataEmpresa['idempresas']]);
        if(!count($dataPromociones))return $this->redirect()->toRoute('home');
        $data = [
            'ordenzona'=> $ordenzona,
            'zona'=> $dataZonas,
            'ordenempresa'=> $ordenempresa,
            'empresa'=> $dataEmpresa,
            'empresaSiguiente'=> $dataEmpresaSiguiente,
            'empresaAnterior'=> $dataEmpresaAnterior,
            'zonaEmpresaSiguiente'=> $dataEmpresaZonaSiguiente,
            'zonaEmpresaAnterior'=> $dataEmpresaZonaAnterior,
            'empresas'=> $dataEmpresas,
            'totalZonas'=> $totalZonas,
            'totalEmpresas'=> $totalEmpresas,
            'expositor'=> $dataExpositor,
            'promociones'=> $dataPromociones,
            'totalProductos'=> count($dataProductos),
            'totalPromociones'=> count($dataPromociones),
            'totalPlanos'=> count($dataPlanos),
            'seo'=> $this->objSeoTable->obtenerSeoFeria($this->layout()->feria['idferias'], 8) //ID 8 PROMOCIONES
        ];
        return new ViewModel($data);
    }

    public function cronogramasAction() {
        $this->validarSecuenciaPagina('contenido');
        $this->layout()->setTemplate('layout/panel');
        $dataPaginaConfiguracion = $this->obtenerPaginaConfiguracion('cronograma');
        $dataPaginaConfiguracion['cronogramas'] = $this->objCronogramasTable->obtenerCronogramasAgrupados($this->layout()->feria['idferias']);
        $dataPaginaConfiguracion['seo'] = $this->objSeoTable->obtenerSeoFeria($this->layout()->feria['idferias'], 18); //ID 18 CRONOGRAMAS
        return new ViewModel($dataPaginaConfiguracion);
    }
    
    public function hallAction() {
        $this->validarSecuenciaPagina('contenido');
        $this->layout()->setTemplate('layout/panel');
        $dataPaginaConfiguracion = $this->obtenerPaginaConfiguracion('hall');
        $dataPaginaConfiguracion['zonas'] = $this->objZonasTable->obtenerZonasPorFeriaOrder($this->layout()->feria['idferias'], 'nombre');
        $dataPaginaConfiguracion['seo'] = $this->objSeoTable->obtenerSeoFeria($this->layout()->feria['idferias'], 22); //ID 22 HALL
        $dataPaginasBotones = $this->objPaginasBotonesTable->obtenerDatoPaginasBotones(['idferias'=> $this->layout()->feria['idferias'], 'idpaginas'=> 11]); //ID 11 Hall
        $dataPaginaConfiguracion['botones'] = ($dataPaginasBotones) ? json_decode($dataPaginasBotones['configuracion'], true) : [];
        $dataPaginaConfiguracion['fondo_video'] = $this->obtenerFondoVideo($dataPaginaConfiguracion['configuracion']);
        return new ViewModel($dataPaginaConfiguracion);
    }

    private function obtenerFondoVideo($configuracion) {
        $activar_fondo_video = (isset($configuracion['activar_fondo_video']) && $configuracion['activar_fondo_video']) ? true : false;
        $ruta_video = (isset($configuracion['fondo_video']) && $configuracion['fondo_video']['hash'] != '') ? $configuracion['fondo_video']['hash'] : '';

        return [
            'estado'=> $activar_fondo_video,
            'ruta'=> $ruta_video
        ];
    }
    
    public function busquedaProductosAction() {
        $this->validarSecuenciaPagina('contenido');
        $this->layout()->setTemplate('layout/panel');
        $data = [
            'seo'=> $this->objSeoTable->obtenerSeoFeria($this->layout()->feria['idferias'], 23) //ID 23 BÚSQUEDA
        ];
        return new ViewModel($data);
    }

    private function obtenerPaginaConfiguracion($hashUrl){
        $dataPagina = $this->objPaginasTable->obtenerDatoPaginas(['hash_url'=> $hashUrl]);
        $dataPaginaFeria = $this->objPaginasFeriasTable->obtenerDatoPaginasFerias(['idpaginas'=> $dataPagina['idpaginas'], 'idferias'=> $this->layout()->feria['idferias']]);
        $dataConfiguracion = ( isset($dataPaginaFeria) && $dataPaginaFeria['configuracion'] != '') ? json_decode($dataPaginaFeria['configuracion'], true) : [];
        return [
            'pagina'=> $dataPagina,
            'configuracion'=> $dataConfiguracion
        ];
    }

    public function listaProductosAction(){
        $q = $this->params()->fromPost('q');
        $dataBuscador = $this->objBuscadorTable->obtenerBuscadorPorFeria($this->layout()->feria['idferias'], $q);
        $dataPromociones = $this->objPromocionesTable->obtenerPromocionesBusquedaPorFeria($this->layout()->feria['idferias'], $q);
        $data = array_merge($dataBuscador, $dataPromociones);
        $results = array_map(function($item) {
            $id = (isset($item['idbuscador'])) ? $item['idbuscador'] : $item['idpromociones'];
            $tipo = (isset($item['idbuscador'])) ? 'B' : 'P';
            $item['_id'] = $id;
            $item['_tipo'] = $tipo;
            return $item;
        }, $data);
        return $this->jsonZF($this->objTools->arrayRandom($results));
    }

    public function standAction() {
        $this->validarSecuenciaPagina('contenido');
        $this->layout()->setTemplate('layout/panel');
        $zona = $this->params()->fromRoute('zona', '');
        $ordenzona = $this->params()->fromRoute('ordenzona', 0);
        $empresa = $this->params()->fromRoute('empresa', '');
        $ordenempresa = $this->params()->fromRoute('ordenempresa', 0);
        $hashurlempresa = $this->params()->fromRoute('hashurlempresa', '');
        if($zona == '' || $ordenzona === 0 || $empresa == '' || $ordenempresa === 0 || $hashurlempresa === '')return $this->redirect()->toRoute('home');
        $dataZonas = $this->objZonasTable->obtenerDatoZonas(['idferias'=> $this->layout()->feria['idferias'], 'orden'=> $ordenzona]);
        $totalZonas = count($this->objZonasTable->obtenerZonasPorFeria($this->layout()->feria['idferias']));
        if(!$dataZonas)return $this->redirect()->toRoute('home');
        // ZONA SIGUIENTE ANTERIOR EMPRESA [INICIO] //
        $dataEmpresaZonaSiguiente = $this->objEmpresasTable->obtenerEmpresaZonaInicial($this->layout()->feria['idferias'], $ordenzona + 1);
        $dataEmpresaZonaAnterior = $this->objEmpresasTable->obtenerEmpresaZonaInicial($this->layout()->feria['idferias'], $ordenzona - 1);
        // ZONA SIGUIENTE EMPRESA [FIN] //
        $dataEmpresa = $this->objEmpresasTable->obtenerDatoEmpresas(['idzonas'=> $dataZonas['idzonas'], 'orden'=> $ordenempresa, 'hash_url'=> $hashurlempresa]);
        if(!$dataEmpresa)return $this->redirect()->toRoute('home');
        $dataEmpresaSiguiente = $this->objEmpresasTable->obtenerDatoEmpresas(['idzonas'=> $dataZonas['idzonas'], 'orden'=> ($ordenempresa + 1)]);
        $dataEmpresaAnterior = $this->objEmpresasTable->obtenerDatoEmpresas(['idzonas'=> $dataZonas['idzonas'], 'orden'=> ($ordenempresa - 1)]);
        $plantillaSeleccionado = '';
        if((int)$dataEmpresa['idstandgaleria']){
            $dataStandGaleria = $this->objStandGaleriaTable->obtenerDatoStandGaleria(['idstandgaleria'=> $dataEmpresa['idstandgaleria']]);
            $dataStand = $this->objStandTable->obtenerDatoStand(['idstand'=> $dataStandGaleria['idstand']]);
            $plantillaSeleccionado = mb_strtolower($dataStand['hash_url']);
        }
        $dataPaginasStand = $this->objPaginasStandTable->obtenerDatoPaginasStand(['idempresas'=> $dataEmpresa['idempresas']]);
        $dataConfiguracion = ( isset($dataPaginasStand) && $dataPaginasStand['configuracion'] != '') ? json_decode($dataPaginasStand['configuracion'], true) : [];
        $totalEmpresas = count($this->objEmpresasTable->obtenerDatosEmpresas($this->layout()->feria['idferias'], $dataZonas['idzonas']));
        $dataEmpresas = $this->objEmpresasTable->obtenerDatoCondicionEmpresas($dataZonas['idzonas']);
        $dataExpositor = $this->objExpositoresTable->obtenerDatosExpositorStand($dataEmpresa['idexpositores']);
        $dataExpositoresProductos = $this->objExpositoresProductosTable->obtenerExpositoresProductos($dataEmpresa['idexpositores']);
        $dataProductos = $this->objProductosTable->obtenerDatosProductos(['idempresas'=> $dataEmpresa['idempresas']]);
        $dataPromociones = $this->objPromocionesTable->obtenerDatosPromociones(['idempresas'=> $dataEmpresa['idempresas']]);
        $dataPlanos = $this->objPlanosTable->obtenerDatosPlanos(['idempresas'=> $dataEmpresa['idempresas']]);
        $dataBanco = $this->objBancosTable->obtenerDatoBancos(['idbancos'=> $dataEmpresa['idbancos']]);
        $data = [
            'ordenzona'=> $ordenzona,
            'zona'=> $dataZonas,
            'ordenempresa'=> $ordenempresa,
            'empresa'=> $dataEmpresa,
            'empresaSiguiente'=> $dataEmpresaSiguiente,
            'empresaAnterior'=> $dataEmpresaAnterior,
            'zonaEmpresaSiguiente'=> $dataEmpresaZonaSiguiente,
            'zonaEmpresaAnterior'=> $dataEmpresaZonaAnterior,
            'empresas'=> $dataEmpresas,
            'totalZonas'=> $totalZonas,
            'totalEmpresas'=> $totalEmpresas,
            'expositor'=> $dataExpositor,
            'productos'=> $dataExpositoresProductos,
            'plantillaSeleccionado'=> $plantillaSeleccionado,
            'configuracion'=> $dataConfiguracion,
            'totalProductos'=> count($dataProductos),
            'totalPromociones'=> count($dataPromociones),
            'totalPlanos'=> count($dataPlanos),
            'banco'=> $dataBanco,
            'seo'=> $this->objSeoTable->obtenerSeoFeria($this->layout()->feria['idferias'], 21), //ID 21 STAND
            'fondo_video'=> $this->obtenerFondoVideo($dataConfiguracion)
        ];

        //print_r($data);
        //die;

        return new ViewModel($data);
    }

    public function zonasAction() {
        $this->validarSecuenciaPagina('contenido');
        $this->layout()->setTemplate('layout/panel');
        $ordenzona = $this->params()->fromRoute('ordenzona', 0);
        $dataZonas = $this->objZonasTable->obtenerDatoZonas(['idferias'=> $this->layout()->feria['idferias'], 'orden'=> $ordenzona]);
        if($ordenzona === 0 || !$dataZonas)return $this->redirect()->toRoute('home');
        $totalZonas = count($this->objZonasTable->obtenerZonasPorFeria($this->layout()->feria['idferias']));
        $dataPaginasZonas = $this->objPaginasZonasTable->obtenerDatoPaginasZonas(['idzonas'=> $dataZonas['idzonas']]);
        $dataConfiguracion = ( isset($dataPaginasZonas['configuracion']) && $dataPaginasZonas['configuracion'] != '') ? json_decode($dataPaginasZonas['configuracion'], true) : [];
        
        $dataEmpresas = $this->objEmpresasTable->obtenerDatoCondicionEmpresas($dataZonas['idzonas']);
        $dataZonasEmpresas = $this->objEmpresasTable->obtenerEmpresasGalerias($dataZonas['idzonas']);
        $data = [
            'zona'=> $dataZonas,
            'ordenzona'=> $ordenzona,
            'configuracion'=> $dataConfiguracion,
            'empresas'=> $dataEmpresas,
            'flechasZonas'=> $this->obtenerFlechasZonas($ordenzona, $totalZonas),
            'zonasEmpresas'=> $dataZonasEmpresas,
            'seo'=> $this->objSeoTable->obtenerSeoFeria($this->layout()->feria['idferias'], 4), //ID 4 ZONAS
            'fondo_video'=> $this->obtenerFondoVideo($dataConfiguracion)
        ];
        return new ViewModel($data);
    }

    private function obtenerFlechasZonas($ordenzona, $totalZonas){
        $flechasZonas = [
            'anterior'=> ['activo'=> 1, 'zona'=> '', 'orden'=> 0],
            'siguiente'=> ['activo'=> 1,'zona'=> '', 'orden'=> 0]
        ];
        //ANTERIOR
        $orderAnterior = $ordenzona - 1;
        if(!$orderAnterior){
            $flechasZonas['anterior']['activo'] = 0;
        } else {
            $flechasZonas['anterior']['activo'] = 1;
            $flechasZonas['anterior']['zona'] = $this->objZonasTable->obtenerDatoZonas(['idferias'=> $this->layout()->feria['idferias'], 'orden'=> $orderAnterior])['nombre'];
            $flechasZonas['anterior']['orden'] = $orderAnterior;
        }
        //SIGUIENTE
        $orderSiguiente = $ordenzona + 1;
        if($orderSiguiente > $totalZonas){
            $flechasZonas['siguiente']['activo'] = 0;
        } else {
            $flechasZonas['siguiente']['activo'] = 1;
            $flechasZonas['siguiente']['zona'] = $this->objZonasTable->obtenerDatoZonas(['idferias'=> $this->layout()->feria['idferias'], 'orden'=> $orderSiguiente])['nombre'];
            $flechasZonas['siguiente']['orden'] = $orderSiguiente;
        }
        return $flechasZonas;
    }

    public function listaOfertasAction(){
        $dataOfertas = $this->objOfertasTable->obtenerDatosOfertas($this->layout()->feria['idferias']);
        return $this->jsonZF($dataOfertas);
    }

    public function enviarDatosTarjetaAction(){
        $idvisitantes = $this->params()->fromPost('idvisitantes');
        $idexpositores = $this->params()->fromPost('idexpositores');
        $dataExpositorTarjeta = $this->objExpositoresTarjetasTable->obtenerDatoExpositoresTarjetas(['idexpositores'=> $idexpositores, 'idvisitantes'=> $idvisitantes]);
        if(!$dataExpositorTarjeta){
            $data = ['idexpositores'=> $idexpositores, 'idvisitantes'=> $idvisitantes, 'fecha_creacion'=> date('Y-m-d H:i:s')];
            $idvisitantes = $this->objExpositoresTarjetasTable->agregarExpositoresTarjetas($data);
        }
        $dataVisitante = $this->objVisitantesTable->obtenerDatoVisitantes(['idvisitantes'=> $idvisitantes]);
        return $this->jsonZF(['result'=> 'success', 'data'=> $dataVisitante]);
    }

    public function juegosAction() {
        $this->validarSecuenciaPagina('contenido');
        $this->layout()->setTemplate('layout/panel');
        return new ViewModel();
    }

    public function auditorioAction() {
        $this->validarSecuenciaPagina('contenido');
        $this->layout()->setTemplate('layout/panel');
        $idcronogramas = $this->params()->fromRoute('idcronograma', '');
        $dataCronograma = ($idcronogramas != '') ? $this->objCronogramasTable->obtenerCronogramaPorId($idcronogramas) : false;
        $tipoAuditorio = ($this->layout()->feria['tipo_auditorio'] == "B") ? 'auditorio_banner_1' : 'auditorio_banner_2';
        $dataConferencias = $this->objConferenciasTable->obtenerConferenciasProgramados($this->layout()->feria['idferias']);
        $cronogramaFechaActual = $this->objCronogramasTable->obtenerCronogramaFechaActual($this->layout()->feria['idferias']);
        $data = [
            'tipoAuditorio'=> $tipoAuditorio,
            'conferencias'=> $dataConferencias,
            'cronogramaFechaActual'=> ($dataCronograma) ? $dataCronograma : $cronogramaFechaActual,
            'seo'=> $this->objSeoTable->obtenerSeoFeria($this->layout()->feria['idferias'], 17), //ID 17 AUDITORIO
        ];
        $data['auditorioImagenesInicial'] = $this->auditorioImagenesInicial($data['cronogramaFechaActual']);
        $data['historialChatsConferencias'] = $this->objChatsGeneralTable->obtenerHistorialChatsConferencias($this->layout()->feria['idferias'], is_countable($data['cronogramaFechaActual']) ? $data['cronogramaFechaActual']['idcronogramas'] : 0);
        $data['totalMensajesChatsConferencias'] = count($data['historialChatsConferencias']);
        //print_r($data['cronogramaFechaActual']);
        return new ViewModel($data);
    }

    private function auditorioImagenesInicial($data){
        $directorioAuditorioFondos = '/auditorio/fondo/';
        $banner_izquierdo_inicial = $this->layout()->feria['config_auditorio_hash_banner_izquierdo_inicial'];
        $banner_derecho_inicial = $this->layout()->feria['config_auditorio_hash_banner_derecho_inicial'];
        $video_centro = $this->layout()->feria['config_auditorio_hash_video_fondo_inicial'];
        $video_no_imagen = '/auditorio/video-inicial.jpg';
        $banner_no_imagen = '/cronogramas/portada-no-imagen.jpg';
        $response = [
            'banner_izquierdo_inicial'=> $banner_no_imagen,
            'banner_derecho_inicial'=> $banner_no_imagen,
            'video_centro'=> $video_no_imagen
        ];
        if(!isset($data['hash_portada_izquierda']) && $banner_izquierdo_inicial != '') {
            $response['banner_izquierdo_inicial'] = $directorioAuditorioFondos.$banner_izquierdo_inicial;
        } 
        if(!isset($data['hash_portada_derecha']) && $banner_derecho_inicial != '') {
            $response['banner_derecho_inicial'] = $directorioAuditorioFondos.$banner_derecho_inicial;
        }
        if($video_centro != '') {
            $response['video_centro'] = '/auditorio/fondo/'.$video_centro;
        }
        return $response;
    }

    public function guardarEventosUsuarioAction(){
        $url_actual = $this->params()->fromPost('url_actual');
        $url_click = $this->params()->fromPost('url_click');
        $video = $this->params()->fromPost('video');
        $whatsapp = $this->params()->fromPost('whatsapp');
        $rv = $this->params()->fromPost('rv');
        $zonas = $this->params()->fromPost('zonas');
        $empresas = $this->params()->fromPost('empresas');
        $banner_izquierda_1 = $this->params()->fromPost('banner_izquierda_1');
        $banner_izquierda_2 = $this->params()->fromPost('banner_izquierda_2');
        $banner_derecha_1 = $this->params()->fromPost('banner_derecha_1');
        $banner_derecha_2 = $this->params()->fromPost('banner_derecha_2');
        $productos = $this->params()->fromPost('productos');
        $promociones = $this->params()->fromPost('promociones');
        $vivo = $this->params()->fromPost('vivo');
        $atencion_inmediata = $this->params()->fromPost('atencion_inmediata');
        $ordenzona = $this->params()->fromPost('ordenzona');
        $ordenempresa = $this->params()->fromPost('ordenempresa');
        $reserva_cita = $this->params()->fromPost('reserva_cita');
        $bf_llamada = $this->params()->fromPost('bf_llamada');
        $bf_wsp = $this->params()->fromPost('bf_wsp');
        //Nuevos Parámetros
        $planos_wsp = $this->params()->fromPost('planos_wsp');
        $planos_solicitar_informacion = $this->params()->fromPost('planos_solicitar_informacion');
        $planos_url = $this->params()->fromPost('planos_url');
        $productos_recorrido360 = $this->params()->fromPost('productos_recorrido360');
        $productos_mapa = $this->params()->fromPost('productos_mapa');
        $productos_brochure = $this->params()->fromPost('productos_brochure');
        $productos_wsp = $this->params()->fromPost('productos_wsp');
        $expositores_vivo_corrreo = $this->params()->fromPost('expositores_vivo_corrreo');
        $expositores_vivo_telefono = $this->params()->fromPost('expositores_vivo_telefono');
        $expositores_vivo_wsp = $this->params()->fromPost('expositores_vivo_wsp');
        $promociones_modal_abrir_enlace = $this->params()->fromPost('promociones_modal_abrir_enlace');
        $promociones_modal_envia_correo = $this->params()->fromPost('promociones_modal_envia_correo');
        $promociones_modal_wsp = $this->params()->fromPost('promociones_modal_wsp');
        $promociones_modal_brochure = $this->params()->fromPost('promociones_modal_brochure');
        //
        $dataZonas = $this->objZonasTable->obtenerDatoZonas(['idferias'=> $this->layout()->feria['idferias'], 'orden'=> $ordenzona]);
        $dataEmpresa = $this->objEmpresasTable->obtenerDatoEmpresas(['idzonas'=> $dataZonas['idzonas'], 'orden'=> $ordenempresa]);
        $data = [
            'idferias'=> $this->layout()->feria['idferias'],
            'idzonas'=> ( $dataZonas ) ? $dataZonas['idzonas'] : 0,
            'idempresas'=> ( $dataEmpresa ) ? $dataEmpresa['idempresas'] : 0,
            'url_referencia'=> $_SERVER['HTTP_REFERER'],
            'url_actual'=> $url_actual,
            'url_click'=> $url_click,
            'ip'=> $_SERVER['REMOTE_ADDR'],
            'fecha_registro'=> date('Y-m-d H:i:s'),
            'idvisitantes'=> ( $this->layout()->datosUsuario['tipo'] == 'V' ) ? $this->layout()->datosUsuario['idvisitantes'] : 0,
            'idexpositores'=> ( $this->layout()->datosUsuario['tipo'] == 'E' ) ? $this->layout()->datosUsuario['idexpositores'] : 0,
            'tipo_usuario'=> @$this->layout()->datosUsuario['tipo'],
            'user_agent'=>  $_SERVER['HTTP_USER_AGENT'],
            'video'=> $video,
            'whatsapp'=> $whatsapp,
            'rv'=> $rv,
            'zonas'=> $zonas,
            'empresas'=> $empresas,
            'banner_izquierda_1'=> $banner_izquierda_1,
            'banner_izquierda_2'=> $banner_izquierda_2,
            'banner_derecha_1'=> $banner_derecha_1,
            'banner_derecha_2'=> $banner_derecha_2,
            'productos'=> $productos,
            'promociones'=> $promociones,
            'vivo'=> $vivo,
            'atencion_inmediata'=> $atencion_inmediata,
            'reserva_cita'=> $reserva_cita,
            'bf_llamada'=> $bf_llamada,
            'bf_wsp'=> $bf_wsp,
            'planos_wsp'=> ($planos_wsp)?$planos_wsp:0,
            'planos_solicitar_informacion'=> ($planos_solicitar_informacion)?$planos_solicitar_informacion:0,
            'planos_url'=> ($planos_url)?$planos_url:0,
            'productos_recorrido360'=> ($productos_recorrido360)?$productos_recorrido360:0,
            'productos_mapa'=> ($productos_mapa)?$productos_mapa:0,
            'productos_brochure'=> ($productos_brochure)?$productos_brochure:0,
            'productos_wsp'=> ($productos_wsp)?$productos_wsp:0,
            'expositores_vivo_corrreo'=> ($expositores_vivo_corrreo)?$expositores_vivo_corrreo:0,
            'expositores_vivo_telefono'=> ($expositores_vivo_telefono)?$expositores_vivo_telefono:0,
            'expositores_vivo_wsp'=> ($expositores_vivo_wsp)?$expositores_vivo_wsp:0,
            'promociones_modal_abrir_enlace'=> ($promociones_modal_abrir_enlace)?$promociones_modal_abrir_enlace:0,
            'promociones_modal_envia_correo'=> ($promociones_modal_envia_correo)?$promociones_modal_envia_correo:0,
            'promociones_modal_wsp'=> ($promociones_modal_wsp)?$promociones_modal_wsp:0,
            'promociones_modal_brochure'=> ($promociones_modal_brochure)?$promociones_modal_brochure:0,
        ];
        $this->objUsuarioEventosTable->agregarUsuarioEventos($data);
        return $this->jsonZF(['result'=>'success']);
    }

    public function enviarCorreoAction(){
        $accion = $this->params()->fromPost('accion');
        $id = $this->params()->fromPost('id');
        $idempresas = $this->params()->fromPost('idempresas');
        $data = [];
        $pathToFile = null;
        $archivoAdjunto = null;
        $rutaDocumento = null;
        $correoPara = null;
        $idbancos = 0;
        $dataEmpresa = $this->objEmpresasTable->obtenerDatoEmpresas(['idempresas'=> $idempresas]);
        
        //Obtener plantilla correos
        $dataPlantillaCorreo = $this->objFeriasCorreosTable->obtenerDatosPlantillaCorreo($this->layout()->feria['idferias'], $accion);
        if(!$dataPlantillaCorreo || !$dataEmpresa){
            return $this->jsonZF(['result'=>'success']);
        }

        //Datos Expositor
        $dataExpositor = $this->objExpositoresTable->obtenerDatoExpositores(['idexpositores'=> $dataEmpresa['idexpositores']]);
        
        //Enviar Correo Copia Expositor
        if(in_array($accion, ["producto", "promocion"]) && $dataExpositor && $dataExpositor['correo'] != "") {
            array_push($dataPlantillaCorreo['correoCopia'], $dataExpositor['correo']);
        }

        //Datos Registro Envio Correos
        $dataEnvioCorreos = [
            'idempresas'=> $idempresas,
            'idexpositores'=> ($dataEmpresa) ? $dataEmpresa['idexpositores'] : 0,
            'titulo'=> $dataPlantillaCorreo['correoTitulo'],
            'idvisitantes'=> (isset($this->sessionContainer['idvisitantes'])) ? $this->sessionContainer['idvisitantes'] : 0,
            'fecha_hora_registro'=> date('Y-m-d H:i'),
            'correos_copia'=> (!empty($dataPlantillaCorreo['correoCopia'])) ? implode(',', $dataPlantillaCorreo['correoCopia']) : "",
            'correo_para'=> "",
            'accion'=> $accion,
            'idbancos'=> $idbancos,
            'idferias'=> $this->sessionContainer['idferias']
        ];

        switch($accion){
            case 'producto':
                $data = $this->objProductosTable->obtenerDatoProductos(['idproductos'=> $id]);
                $rutaDocumento = $this->layout()->url_backend."/productos/documentos";
                if($data['tipo_enlace'] == 'PDF' && $data['hash_pdf'] != null){
                    $pathToFile = $rutaDocumento."/".$data['hash_pdf'];
                    $archivoAdjunto = $data['nombre_pdf'];
                }
                $correoPara = $this->sessionContainer['correo'];
            break;
            case 'promocion':
                $data = $this->objPromocionesTable->obtenerDatoPromociones(['idpromociones'=> $id]);
                $rutaDocumento = $this->layout()->url_backend."/promociones/documentos";
                if($data['tipo_enlace'] == 'PDF' && $data['hash_pdf'] != null){
                    $pathToFile = $rutaDocumento."/".$data['hash_pdf'];
                    $archivoAdjunto = $data['nombre_pdf'];
                }
                $correoPara = $this->sessionContainer['correo'];
            break;
            case 'banco':
                $data = $this->objBancosTable->obtenerDatoBancos(['idbancos'=> $id]);
                $correoPara = trim($data['correo']);
                $idbancos = $id;
            break;
            case 'expositor':
                $data = $this->objExpositoresTable->obtenerDatoExpositores(['idexpositores'=> $id]);
                $correoPara = trim($data['correo']);
            break;
            case 'chat-notificacion':
                $data = [
                    'visitante'=> $this->objVisitantesTable->obtenerDatoVisitantes(['idvisitantes'=> $id]),
                    'empresa'=> $dataEmpresa
                ];
                $correoPara = trim($data['visitante']['correo']);
            break;
            default:
            
            break;
        }

        //Datos Registro Envio Correos
        $dataEnvioCorreos["correo_para"] = $correoPara;
        $dataEnvioCorreos["idbancos"] = $idbancos;
        $this->objRegistroEnvioCorreosTable->agregarRegistroEnvioCorreos($dataEnvioCorreos);

        //Enviar Correo
        if(!empty($data)){
            $mailDatos = [
                'accion'=> $accion,
                'informacion'=> $data,
                'visitante'=> $this->layout()->datosUsuario,
                'contenidoCorreo'=> $dataPlantillaCorreo['contenidoCorreo']
            ];
            $this->objMailSender->sendMail($correoPara,$dataPlantillaCorreo['correoTitulo'],$mailDatos,$accion,$pathToFile,$archivoAdjunto,$dataPlantillaCorreo['correoCopia'],$this->layout()->feria['nombre']);
        }
        
        return $this->jsonZF(['result'=>'success']);
    }

    public function agendarReunionVirtualAction(){
        $fecha = $this->params()->fromPost('fecha');
        $hora = $this->params()->fromPost('hora');
        $comentario = $this->params()->fromPost('comentario');
        $idempresas = $this->params()->fromPost('idempresas');
        $correoPara = $this->sessionContainer['correo'];
        $plantillaCorreo = "agenda-virtual";

        //Obtener plantilla correos
        $dataPlantillaCorreo = $this->objFeriasCorreosTable->obtenerDatosPlantillaCorreo($this->layout()->feria['idferias'], $plantillaCorreo);
        $dataEmpresa = $this->objEmpresasTable->obtenerDatoEmpresas(['idempresas'=> $idempresas]);
        if(!$dataPlantillaCorreo || !$dataEmpresa){
            return $this->jsonZF(['result'=>'success']);
        }
        
        //Datos Expositor
        $dataExpositor = $this->objExpositoresTable->obtenerDatoExpositores(['idexpositores'=> $dataEmpresa['idexpositores']]);
        if($dataExpositor && $dataExpositor['correo'] != "") {
            array_push($dataPlantillaCorreo['correoCopia'], $dataExpositor['correo']);
        }

        //Datos Registro Envio Correos
        $dataEnvioCorreos = [
            'idempresas'=> $idempresas,
            'idexpositores'=> ($dataEmpresa) ? $dataEmpresa['idexpositores'] : 0,
            'titulo'=> $dataPlantillaCorreo['correoTitulo'],
            'idvisitantes'=> (isset($this->sessionContainer['idvisitantes'])) ? $this->sessionContainer['idvisitantes'] : 0,
            'fecha_hora_registro'=> date('Y-m-d H:i'),
            'correos_copia'=> (!empty($dataPlantillaCorreo['correoCopia'])) ? implode(',', $dataPlantillaCorreo['correoCopia']) : "",
            'correo_para'=> $correoPara,
            'accion'=> $plantillaCorreo,
            'idbancos'=> 0,
            'idferias'=> $this->sessionContainer['idferias']
        ];
        $this->objRegistroEnvioCorreosTable->agregarRegistroEnvioCorreos($dataEnvioCorreos);

        //Enviar Correo
        $mailDatos = [
            'informacion'=> [
                'fecha'=> $fecha,
                'hora'=> $hora,
                'comentario'=> $comentario,
            ],
            'contenidoCorreo'=> $dataPlantillaCorreo['contenidoCorreo'],
            'visitante'=> $this->layout()->datosUsuario
        ];
        $this->objMailSender->sendMail($correoPara,$dataPlantillaCorreo['correoTitulo'],$mailDatos,$plantillaCorreo,null,null,$dataPlantillaCorreo['correoCopia'],$this->layout()->feria['nombre']);  
        return $this->jsonZF(['result'=>'success']);
    }

    public function busquedaNetworkingAction() {
        $this->validarSecuenciaPagina('contenido');
        $this->layout()->setTemplate('layout/panel');
        $data = [
            'seo'=> $this->objSeoTable->obtenerSeoFeria($this->layout()->feria['idferias'], 26) //ID 26 BÚSQUEDA NETWORKING
        ];
        return new ViewModel($data);
    }

    public function listarNetworkingAction(){
        $q = $this->params()->fromPost('q');
        $data = $this->objExpositoresTable->obtenerBusquedaExpositores($this->layout()->feria['idferias'], $q);
        return $this->jsonZF($this->objTools->arrayRandom($data));
    }

    public function networkingAction() {
        $this->validarSecuenciaPagina('contenido');
        $this->layout()->setTemplate('layout/panel');
        $id = $this->params()->fromRoute('id', '');
        $dataExpositor = $this->objExpositoresTable->obtenerDatoExpositores(['idexpositores'=> $id]);

        if($id === '' || !$dataExpositor)return $this->redirect()->toRoute('home');

        $dataEmpresas = $this->objEmpresasTable->obtenerZonasEmpresasPorFeria($this->layout()->feria['idferias'], 10);
        $data = [
            'expositor'=> $dataExpositor,
            'empresas'=> $dataEmpresas,
            'empresa'=> ['nombre'=> '', 'idempresas'=> 0],
            'estadoAgendaVirtualUsuario'=> $this->validarOpcionAtencionVirtualUsuario(0),
            'seo'=> $this->objSeoTable->obtenerSeoFeria($this->layout()->feria['idferias'], 25) //ID 25 SPEAKERS VIVO
        ];

        return new ViewModel($data);
    }

    private function obtenerMenusPiePagina($empresa, $totalProductos, $totalPromociones, $totalPlanos){
        
        $result = [];

        if(!empty($this->layout()->menus['piePagina']))  {
            
            foreach( $this->layout()->menus['piePagina'] as $item) {
     
                $href = $item['hash_url'];
                $target = '';
                $ocultarUrl = '';
                $bloquearEnlace = '';
                $funcion = '';
                $hrefClass = '';
                $atributoImg = '<img src="imagenes/tin.svg">';
                if(!empty($this->empresa)){
                    if($href == 'realidad-virtual'){
                        $href = $this->layout()->idiomaSeleccionado.'/panel/'.$href.'/'.$this->empresa['idempresas'];
                    } else if($href == 'productos') {
                        if($this->totalProductos == 0) {
                            $bloquearEnlace = 'style="display:none"';
                        }
                        $hrefClass = "productos";
                        $href = $this->layout()->idiomaSeleccionado.'/panel/'.$href.'/zona/'.$this->zona['orden'].'/empresa/'.$this->empresa['orden'].'/'.$this->empresa['hash_url'];
                    } else if($href == 'promociones') {
                        if($this->totalPromociones == 0) {
                            $bloquearEnlace = 'style="display:none"';
                        }
                        $hrefClass = "promociones";
                        $href = $this->layout()->idiomaSeleccionado.'/panel/'.$href.'/zona/'.$this->zona['orden'].'/empresa/'.$this->empresa['orden'].'/'.$this->empresa['hash_url'];
                    } else if($href == 'planos') {
                        if($this->totalPlanos == 0) {
                            $bloquearEnlace = 'style="display:none"';
                        }
                        $hrefClass = "planos";
                        $href = $this->layout()->idiomaSeleccionado.'/panel/'.$href.'/zona/'.$this->zona['orden'].'/empresa/'.$this->empresa['orden'].'/'.$this->empresa['hash_url'];
                    } else if($href == 'en-vivo') {
                        $href = $this->layout()->idiomaSeleccionado.'/panel/expositores-vivo/zona/'.$this->zona['orden'].'/empresa/'.$this->empresa['orden'].'/'.$this->empresa['hash_url'];
                        $hrefClass = "resaltar en-vivo";
                    } else if ($href == 'informacion') {
                        $href = "javascript:abrirVentanaWindow('".$this->empresa['enlace_web']."')";
                        $hrefClass = "informacion";
                    } else if ($href == 'send-business-card'){
                        if( $this->layout()->datosUsuario['tipo'] == 'E' ) $ocultarUrl = 'style="display:none"';
                        $href = $this->layout()->idiomaSeleccionado.'/panel/'.$href;
                        $funcion = 'PiePagina.enviarTarjeta(this, event)';
                    } else {
                        $href = $this->layout()->idiomaSeleccionado.'/panel/'.$href;
                    }
                    if($this->accion != 'expositores-vivo' && $href == $this->layout()->idiomaSeleccionado.'/panel/send-business-card'){
                        $ocultarUrl = 'style="display:none"';
                    }
                }

                /* <!-- <a href="<?php echo $href; ?>" onclick="<?php echo $funcion; ?>" <?php echo $ocultarUrl; ?> <?php echo $bloquearEnlace; ?> <?php echo $target; ?> class="<?php echo ( $this->layout()->menus['hash_url_activo'] == $item['hash_url'] ) ? 'resaltar' : ''; ?> <?php echo $hrefClass; ?>"><?php echo $atributoImg; ?>&nbsp;<?php echo $this->layout()->language->translate(mb_strtoupper($item['nombre']))['text']; ?></a> --> */

                $result[] = [
                    'href'=> $href,
                    'funcion'=> $funcion,
                    'ocultarUrl'=> $ocultarUrl,
                    'bloquearEnlace'=> $bloquearEnlace,
                    'target'=> $target,
                    'hrefClass'=> $hrefClass,
                    'atributoImg'=> $atributoImg,
                    'menu'=> $item['nombre']
                ];

            }
        }

        return $result;

    }

    public function datosVisitanteAction() {
        $idvisitante = $this->params()->fromQuery('id');
        $visitante = $this->objVisitantesTable->obtenerDatoVisitantes(['idvisitantes'=> $idvisitante]);

        if($visitante){
            return $this->jsonZF($visitante);
        } else {
            return $this->jsonZF([]);
        }
    }

    private function jsonZF($data){
        return new JsonModel($data);
    }

}
