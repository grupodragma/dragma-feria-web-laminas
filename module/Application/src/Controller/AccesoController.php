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
use Application\Model\AccesoTable;
use Laminas\Crypt\Password\Bcrypt;

class AccesoController extends AbstractActionController {

    protected $serviceManager;
    private $authManager;
    protected $objVisitantesTable;
    protected $objFeriasTable;
    protected $objMailSender;
    protected $objExpositoresTable;
    protected $objFeriasCorreosTable;
    protected $objVisitantesRegistrosTable;
    protected $sessionFormSession;
    protected $salt;
    protected $objUsuarioTable;

    public function __construct($serviceManager, $authManager, $objVisitantesTable, $objFeriasTable, $objMailSender, $objExpositoresTable, $objFeriasCorreosTable, $objVisitantesRegistrosTable, $objUsuarioTable) {
        $this->serviceManager = $serviceManager;
        $this->authManager = $authManager;
        $this->objVisitantesTable = $objVisitantesTable;
        $this->objFeriasTable = $objFeriasTable;
        $this->objMailSender = $objMailSender;
        $this->objExpositoresTable = $objExpositoresTable;
        $this->objFeriasCorreosTable = $objFeriasCorreosTable;
        $this->objVisitantesRegistrosTable = $objVisitantesRegistrosTable;
        $this->sessionFormSession = $this->serviceManager->get('FormSession');
        $this->salt = '::::::(`_´)::::: NCL/SECURE';
        $this->objUsuarioTable = $objUsuarioTable;
    }
    
    public function loginAction() {

        if(!$this->getRequest()->isPost()) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $datosFormulario = $this->params()->fromPost();

        //Validar Csrf
        if($this->sessionFormSession->csrf_token != $datosFormulario['csrf_token']){
            $this->getResponse()->setStatusCode(404);
            return;
        }

        unset($datosFormulario['csrf_token']);

        $numeroDocumento = @trim($datosFormulario['dni']);
        $fechaHoraActual = date('Y-m-d H:i:s');

        if(empty($datosFormulario))return new JsonModel(['result'=>'error']);

        //Datos Registro Visitante
        $datosRegistroVisitante = [
            'idferias'=> $this->layout()->feria['idferias'],
            'fecha_registro'=> $fechaHoraActual,
            'tipo'=> 'V'
        ];

        $datosFormulario['idferias'] = $this->layout()->feria['idferias'];
        $datosFormulario['numero_documento'] = $numeroDocumento;
        $datosFormulario['contrasena'] = md5($numeroDocumento);
        $datosFormulario['fecha_registro_web'] = $fechaHoraActual;
        $datosFormulario['fecha_creacion'] =  $fechaHoraActual;
        $datosFormulario['fecha_ultima_sesion'] = $fechaHoraActual;
        
        unset($datosFormulario['dni']);

        $dataUsuario = $this->obtenerDatosUsuario($numeroDocumento);
        $resultStatus = $this->validarExisteUsuario($numeroDocumento);

        if ($resultStatus == 'SUCCESS') {
            unset($datosFormulario['idferias']);
            unset($datosFormulario['fecha_creacion']);
            unset($datosFormulario['numero_documento']);
            unset($datosFormulario['contrasena']);
            if(!isset($datosFormulario['condicion']))unset($datosFormulario['condicion']);
            if($dataUsuario['fecha_registro_web'] != '')unset($datosFormulario['fecha_registro_web']);
            if($dataUsuario['tipo'] === 'V'){
                //Agregar Registro Visitante
                $datosRegistroVisitante['idvisitantes'] = $dataUsuario['idvisitantes'];
                $this->objVisitantesRegistrosTable->agregarVisitantesRegistros($datosRegistroVisitante);
                //Actualizar Datos Visitante
                $datosActualizar = $this->validarCamposActualizar($datosFormulario);
                $this->objVisitantesTable->actualizarDatosVisitantes($datosActualizar, $dataUsuario['idvisitantes']);
            }
            unset($dataUsuario['contrasena']);
            //Eliminar Csrf Token 
            unset($this->sessionFormSession->csrf_token);
            return new JsonModel(['result'=>'success', 'usuario'=> $dataUsuario]);
        } else if ($resultStatus == 'BANNED'){
            //Eliminar Csrf Token 
            unset($this->sessionFormSession->csrf_token);
            return new JsonModel(['result'=>'excess_error']);
        } else if ($resultStatus == 'NOACTIVATE'){
            //Eliminar Csrf Token 
            unset($this->sessionFormSession->csrf_token);
            return new JsonModel(['result'=>'noactivate']);
        } else if ($resultStatus == 'CREDENTIAL_INVALID'){
            /******* ENVIAR CORREO PARA VISITANTES NUEVOS [INICIO] *******/
            $plantillaCorreo = "visitante";
            $dataPlantillaCorreo = $this->objFeriasCorreosTable->obtenerDatosPlantillaCorreo($this->layout()->feria['idferias'], $plantillaCorreo);
            if($dataPlantillaCorreo){
                $mailDatos = [
                    'base_url'=> $this->layout()->base_url,
                    'visitante'=> $datosFormulario,
                    'feria'=> $this->layout()->feria,
                    'contenidoCorreo'=> $dataPlantillaCorreo['contenidoCorreo'],
                    'datosCorreo'=> $dataPlantillaCorreo['correoDatosAdicionales']
                ];
                $this->objMailSender->sendMail($datosFormulario['correo'],$dataPlantillaCorreo['correoTitulo'],$mailDatos,$plantillaCorreo,null,null,$dataPlantillaCorreo['correoCopia'],$this->layout()->feria['nombre']);
            }
            /******* ENVIAR CORREO PARA VISITANTES NUEVOS [FIN] *******/
            $lastIdVisitante = $this->objVisitantesTable->agregarVisitantes($datosFormulario);
            //Agregar Registro Visitante
            $datosRegistroVisitante['idvisitantes'] = $lastIdVisitante;
            $this->objVisitantesRegistrosTable->agregarVisitantesRegistros($datosRegistroVisitante);
            //Obtener Datos Visitante
            $dataUsuario = $this->objVisitantesTable->obtenerDatoVisitantes(['idferias'=> $this->layout()->feria['idferias'], 'numero_documento'=> $numeroDocumento]);
            $this->validarExisteUsuario($numeroDocumento);
            unset($dataUsuario['contrasena']);
            //Eliminar Csrf Token 
            unset($this->sessionFormSession->csrf_token);
            return new JsonModel(['result'=>'success', 'usuario'=> $dataUsuario]);
        } else {
            return new JsonModel(['result'=>'error']);
        }
    }

    public function loginPresencialAction() {

        if(!$this->getRequest()->isPost()) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $datosFormulario = $this->params()->fromPost();
        $numeroDocumento = $datosFormulario['dni'];

        if($this->sessionFormSession->csrf_token != $datosFormulario['csrf_token']){
            $this->getResponse()->setStatusCode(404);
            return;
        }

        if(empty($datosFormulario)){
            return new JsonModel(['result'=>'error']);
        }

        $fechaHoraActual = date('Y-m-d H:i:s');
        $visitante = $this->objVisitantesTable->obtenerDatoVisitantes(['idferias'=> $this->layout()->feria['idferias'], 'numero_documento'=> trim($numeroDocumento)]);

        //Datos Registro Visitante
        $datosRegistroVisitante = [
            'idferias'=> $this->layout()->feria['idferias'],
            'fecha_registro'=> $fechaHoraActual,
            'tipo'=> 'P',
            'idusuario'=> $datosFormulario['idusuario']
        ];

        unset($datosFormulario['dni']);
        unset($datosFormulario['csrf_token']);

        $datosFormulario['idferias'] = $this->layout()->feria['idferias'];
        $datosFormulario['numero_documento'] = $numeroDocumento;
        $datosFormulario['contrasena'] = md5($numeroDocumento);
        $datosFormulario['fecha_registro_web'] = $fechaHoraActual;
        $datosFormulario['fecha_creacion'] =  $fechaHoraActual;
        $datosFormulario['fecha_ultima_sesion'] = $fechaHoraActual;

        $datosVisitante = $this->validarCamposActualizar($datosFormulario);

        if($visitante){
            $datosRegistroVisitante['idvisitantes'] = $visitante['idvisitantes'];
            //Actualizar Datos Visitante
            $this->objVisitantesTable->actualizarDatosVisitantes($datosVisitante, $visitante['idvisitantes']);
        } else {
            //Enviar Correo
            $plantillaCorreo = "visitante";
            $dataPlantillaCorreo = $this->objFeriasCorreosTable->obtenerDatosPlantillaCorreo($this->layout()->feria['idferias'], $plantillaCorreo);
            if($dataPlantillaCorreo){
                $mailDatos = [
                    'base_url'=> $this->layout()->base_url,
                    'visitante'=> $datosFormulario,
                    'feria'=> $this->layout()->feria,
                    'contenidoCorreo'=> $dataPlantillaCorreo['contenidoCorreo'],
                    'datosCorreo'=> $dataPlantillaCorreo['correoDatosAdicionales']
                ];
                $this->objMailSender->sendMail($datosFormulario['correo'],$dataPlantillaCorreo['correoTitulo'],$mailDatos,$plantillaCorreo,null,null,$dataPlantillaCorreo['correoCopia'],$this->layout()->feria['nombre']);
            }
            //Registrar Datos Visitante
            $lastIdVisitante = $this->objVisitantesTable->agregarVisitantes($datosVisitante);
            $datosRegistroVisitante['idvisitantes'] = $lastIdVisitante;
        }

        //Agregar Registro Visitante
        $this->objVisitantesRegistrosTable->agregarVisitantesRegistros($datosRegistroVisitante);

        //Eliminar Csrf Token 
        unset($this->sessionFormSession->csrf_token);

        return new JsonModel(['result'=> 'success']);
    }

    public function loginRecepcionAction() {

        if(!$this->getRequest()->isPost()) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $datosFormulario = $this->params()->fromPost();

        if($this->sessionFormSession->csrf_token != $datosFormulario['csrf_token']){
            $this->getResponse()->setStatusCode(404);
            return;
        }

        if(empty($datosFormulario)){
            return new JsonModel(['result'=>'error']);
        }

        $usuario = $datosFormulario['usuario'];
        $contrasena = $datosFormulario['contrasena'];

        //Eliminar Csrf Token 
        unset($this->sessionFormSession->csrf_token);

        $dataUsuario = $this->objUsuarioTable->obternerDatoUsuario(['usuario'=> trim($usuario), 'contrasena'=> md5(trim($this->salt.$contrasena))]);

        if(!$dataUsuario){
            return new JsonModel(['result'=> 'no_existe']);
        }

        unset($dataUsuario['contrasena']);

        return new JsonModel(['result'=> 'success', 'datos'=> $dataUsuario]);
        
    }

    private function validarCamposActualizar($datos){
        $campos = [];
        if(!empty($datos)){
            foreach($datos as $campo => $valor){
                if($valor != ''){
                    $campos[$campo] = $valor;
                }
            }
        }
        return $campos;
    }

    private function obtenerDatosUsuario($numeroDocumento){
        $dataUsuario = $this->objVisitantesTable->obtenerDatoVisitantes(['idferias'=> $this->layout()->feria['idferias'], 'numero_documento'=> $numeroDocumento]);
        if(!$dataUsuario){
            $dataUsuario = $this->objExpositoresTable->obtenerDatoExpositores(['idferias'=> $this->layout()->feria['idferias'], 'numero_documento'=> $numeroDocumento]);
        }
        return $dataUsuario;
    }

    private function validarExisteUsuario($numeroDocumento){
        $result = $this->authManager->login($numeroDocumento, $this->layout()->feria['idferias']);
        $estatus = $result->getMessages();
        return $estatus[0];
    }
    
    public function logoutAction() {
        $this->authManager->logout();
        return $this->redirect()->toUrl('/'.$this->layout()->idiomaSeleccionado);
    }

    public function generarCsrfTokenAction(){
        $bcrypt = new Bcrypt();
        $this->sessionFormSession->csrf_token = $bcrypt->create(md5(uniqid()));
        return new JsonModel(['csrf_token'=> $this->sessionFormSession->csrf_token]);
    }

}