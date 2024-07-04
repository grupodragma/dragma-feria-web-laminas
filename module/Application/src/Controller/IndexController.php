<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\Crypt\Password\Bcrypt;

class IndexController extends AbstractActionController {

    protected $serviceManager;
    protected $objFeriasTable;
    protected $objPaginasFeriasTable;
    protected $objPaginasTable;
    private $salt = '::::::(`_´)::::: NCL/SECURE';
    protected $objVisitantesTable;
    protected $sessionContainer;
    protected $objSeoTable;
    protected $secuencia;
    protected $sessionFormSession;

    public function __construct($serviceManager, $objFeriasTable, $objPaginasFeriasTable, $objPaginasTable, $objVisitantesTable, $objSeoTable) {
        $this->serviceManager = $serviceManager;
        $this->objFeriasTable = $objFeriasTable;
        $this->objPaginasFeriasTable = $objPaginasFeriasTable;
        $this->objPaginasTable = $objPaginasTable;
        $this->objVisitantesTable = $objVisitantesTable;
        $this->sessionContainer = $this->serviceManager->get('DatosSession')->datosUsuario;
        $this->objSeoTable = $objSeoTable;
        $this->secuencia = $this->layout()->menuSecuencia;
        $this->sessionFormSession = $this->serviceManager->get('FormSession');
    }

    private function validarSecuenciaPagina($accion){
        $this->secuencia = $this->layout()->menuSecuencia;
        if( !$this->secuencia ) {
            die('>>> Plataforma no disponible');
        }
        switch($accion) {
            case 'home':
                if( !(int)$this->secuencia['home']['estado'] ) {
                    return $this->redirect()->toUrl('/'.$this->layout()->idiomaSeleccionado.'/registro');
                }
            break;
            case 'registro':
                if( !(int)$this->secuencia['registro']['estado'] ) {
                    if(!(int)$this->secuencia['contenido']['estado']){
                        die('>>> Plataforma no disponible');
                    }
                    return $this->redirect()->toUrl($this->secuenciaPaginaRedireccion('contenido'));
                }
            break;
            default:
            break;
        }
    }
    
    private function secuenciaPaginaRedireccion($accion){
        $urlPath = ( $this->layout()->menuSecuencia[$accion]['menu_posicion'] === 'C') ? 'panel/' : '';
        return $urlPath.$this->layout()->menuSecuencia[$accion]['menu_url'];
    }

    public function indexAction() {
        $this->validarSecuenciaPagina('home');
        $dataPaginaConfiguracion = $this->obtenerPaginaConfiguracion('home');
        $dataPaginaConfiguracion['seo'] = $this->objSeoTable->obtenerSeoFeria($this->layout()->feria['idferias'], 19); //ID 19 HOME
        $dataPaginaConfiguracion['urlRedirect'] = $this->secuenciaPaginaRedireccion('home');
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

    public function registroAction() {
        $this->validarSecuenciaPagina('registro');
        if($this->sessionContainer){
            if( (int)$this->secuencia['registro']['estado'] ) {
                return $this->redirect()->toUrl('/'.$this->layout()->idiomaSeleccionado.'/'.$this->secuenciaPaginaRedireccion('registro'));
            }
            if( (int)$this->secuencia['contenido']['estado'] ) {
                return $this->redirect()->toUrl('/'.$this->layout()->idiomaSeleccionado.'/'.$this->secuenciaPaginaRedireccion('contenido'));
            }
        }
        $this->layout()->setTemplate('layout/registro');
        $bcrypt = new Bcrypt();
        $this->sessionFormSession->csrf_token = $bcrypt->create(md5(uniqid()));
        $dataPaginaConfiguracion = $this->obtenerPaginaConfiguracion('registro');
        $dataPaginaConfiguracion['seo'] = $this->objSeoTable->obtenerSeoFeria($this->layout()->feria['idferias'], 20); //ID 20 REGISTRO
        $dataPaginaConfiguracion['urlRedirect'] = $this->secuenciaPaginaRedireccion('registro');
        $dataPaginaConfiguracion['csrf_token'] = $this->sessionFormSession->csrf_token;
        $dataPaginaConfiguracion['fondo_video'] = $this->obtenerFondoVideo($dataPaginaConfiguracion['configuracion']);
        return new ViewModel($dataPaginaConfiguracion);
    }

    public function visitanteAction() {
        $idvisitantes = $this->params()->fromQuery('id');
        $hash = $this->params()->fromQuery('hash');
        if($hash != md5($this->salt.$idvisitantes)) {
            return $this->redirect()->toUrl('/'.$this->layout()->idiomaSeleccionado.'/registro');
        }
        $dataVisitante = $this->objVisitantesTable->obtenerDatoVisitantes(['idvisitantes'=> $idvisitantes]);
        return new ViewModel($dataVisitante);
    }

    private function obtenerPaginaConfiguracion($hashUrl){
        $dataPagina = $this->objPaginasTable->obtenerDatoPaginas(['hash_url'=> $hashUrl]);
        $dataPaginaFeria = $this->objPaginasFeriasTable->obtenerDatoPaginasFerias(['idpaginas'=> $dataPagina['idpaginas'], 'idferias'=> $this->layout()->feria['idferias']]);
        $dataConfiguracion = ( $dataPaginaFeria['configuracion'] != '') ? json_decode($dataPaginaFeria['configuracion'], true) : [];
        return [
            'pagina'=> $dataPagina,
            'configuracion'=> $dataConfiguracion
        ];
    }

    public function registroPresencialAction() {
        $this->layout()->setTemplate('layout/registro');
        $qr = $this->params()->fromQuery('qr');
        $bcrypt = new Bcrypt();
        $this->sessionFormSession->csrf_token = $bcrypt->create(md5(uniqid()));
        $dataPaginaConfiguracion = $this->obtenerPaginaConfiguracion('registro-presencial');
        $dataPaginaConfiguracion['csrf_token'] = $this->sessionFormSession->csrf_token;
        $dataPaginaConfiguracion['qr'] = ( isset($qr) ) ? 'OK' : '';
        return new ViewModel($dataPaginaConfiguracion);
    }

}
