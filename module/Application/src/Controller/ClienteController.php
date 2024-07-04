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
use Laminas\Validator\Date as DateFormat;

class ClienteController extends AbstractActionController {

    protected $serviceManager;
    protected $objVisitantesTable;
    protected $objFeriasTable;
    protected $objExpositoresTable;
    protected $objAgendaVirtualTable;
    protected $objMailSender;
    protected $objFeriasCorreosTable;

    public function __construct($serviceManager, $objVisitantesTable, $objFeriasTable, $objExpositoresTable, $objAgendaVirtualTable, $objMailSender, $objFeriasCorreosTable) {
        $this->serviceManager = $serviceManager;
        $this->objVisitantesTable = $objVisitantesTable;
        $this->objFeriasTable = $objFeriasTable;
        $this->objExpositoresTable = $objExpositoresTable;
        $this->objAgendaVirtualTable = $objAgendaVirtualTable;
        $this->dateFormat = new DateFormat(['format' => 'd/m/Y H:i']);
        $this->sessionContainer = $this->serviceManager->get('DatosSession')->datosUsuario;
        $this->objMailSender = $objMailSender;
        $this->objFeriasCorreosTable = $objFeriasCorreosTable;
    }
    
    public function indexAction() {
        return new ViewModel();
    }

    public function validarNumeroDocumentoAction(){
        $datosFormulario = $this->params()->fromPost();
        $dataVisitante = $this->objVisitantesTable->obtenerDatoVisitantes(['numero_documento'=> $datosFormulario['numero_documento'], 'idferias'=> $this->layout()->feria['idferias']]);
        if(!$dataVisitante){
            $dataVisitante = $this->objExpositoresTable->obtenerDatoExpositores(['numero_documento'=> $datosFormulario['numero_documento'], 'idferias'=> $this->layout()->feria['idferias']]);
        }
        $response = [];
        if($dataVisitante)$response = ['result'=>'existe', 'datos'=> $dataVisitante];
        else $response = ['result'=>'noexiste'];
        return $this->jsonZF($response);
    }

    public function validarExisteVisitanteAction(){
        $numeroDocumento = $this->params()->fromPost('numero_documento');
        if(!$this->getRequest()->isPost()) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        $visitante = $this->objVisitantesTable->obtenerDatoVisitantes(['idferias'=> $this->layout()->feria['idferias'], 'numero_documento'=> trim($numeroDocumento)]);
        $response = [];
        if($visitante){
            $response = $this->jsonZF(['ok'=> true, 'datos'=> $visitante]);
        } else {
            $response = $this->jsonZF(['ok'=> false]);
        }
        return $response;
    }

    public function validarAgendasDisponiblesAction(){
        $fecha = $this->params()->fromPost('fecha');
        $idempresas = $this->params()->fromPost('idempresas');
        $idUsuarioSesion = $this->obtenerIdUsuarioSesion();
        $response = ['result'=> 'success'];

        if($this->sessionContainer && $this->dateFormat->isValid($fecha) && $idUsuarioSesion){
            if ($this->objAgendaVirtualTable->validarRangoAgendaVirtual($fecha)) {
                $horariosDisponibles = $this->obtenerHorariosDisponibles($fecha, $idempresas);
                $response = [
                    'result'   => 'success',
                    'agenda_virtual' => $horariosDisponibles
                ];
            } else {
                $response = ['result'=> 'not-range-date'];
            }
        }

        return $this->jsonZF($response);
    }

    private function obtenerHorariosDisponibles($dateUser, $idempresas){
        $dateUserSelected = date('Y-m-d H:i', strtotime(str_replace("/","-", $dateUser)));
        $dateUserFormatSelected = date('Y-m-d', strtotime(str_replace("/","-", $dateUserSelected)));
        
        if( (int)$idempresas > 0 ) {
            $agendaVirtualFechas = $this->objAgendaVirtualTable->obtenerAgendaVirtualPorFecha($dateUserFormatSelected, $idempresas);
        } else {
            $agendaVirtualFechas = $this->objAgendaVirtualTable->obtenerAgendaVirtualPorFechaSinEmpresa($dateUserFormatSelected);
        }

        $rangoFechasValidas = $this->obtenerRangoFechasValidas($dateUserFormatSelected);
        $dataResponseHorarios = [];
        $dataResponseHorarios['usuario_fecha_disponible'] = true;
        $dataResponseHorarios['horarios_disponibles'] = [];
        if(!empty($agendaVirtualFechas)){
            $dataFechasAgendasReservados = [];
            foreach($agendaVirtualFechas as $agenda) {
                $fechaAgendaInicioDB = date("Y-m-d H:i", strtotime($agenda['fecha_agenda_inicio']));
                if( $dateUserSelected === $fechaAgendaInicioDB) {
                    $dataResponseHorarios['usuario_fecha_disponible'] = false;
                }
                $dataFechasAgendasReservados[] = $fechaAgendaInicioDB;
            }
            foreach($rangoFechasValidas as $fecha) {
                if( in_array($fecha['fecha'], $dataFechasAgendasReservados) ) {
                    $fecha['estado'] = false;
                }
                $dataResponseHorarios['horarios_disponibles'][] = $fecha;
            }
        } else {
            $dataResponseHorarios['horarios_disponibles'] = $rangoFechasValidas;
        }
        return $dataResponseHorarios;
    }

    public function obtenerHorariosDisponiblesAction(){
        $fecha = $this->params()->fromPost('fecha');
        $idempresas = $this->params()->fromPost('idempresas');
        $horariosDisponibles = $this->obtenerHorariosDisponibles($fecha, $idempresas);
        return $this->jsonZF([
            'result'   => 'success',
            'agenda_virtual' => $horariosDisponibles
        ]);
    }

    private function obtenerRangoFechasValidas($dateUserFormatSelected){
        $rangoFechas = [];
        $begin = new \DateTime($dateUserFormatSelected." 09:00");
        $end   = new \DateTime($dateUserFormatSelected." 20:00");
        for($i = $begin; $i <= $end; $i->modify('+30 minutes')){
            $rangoFechas[] = [
                'estado'=> true,
                'fecha'=> $i->format("Y-m-d H:i")
            ];
        }
        return $rangoFechas;
    }

    public function guardarAgendaVirtualAction(){
        $fecha = $this->params()->fromPost('fecha');
        $idempresas = $this->params()->fromPost('idempresas');
        $comentario = $this->params()->fromPost('comentario');
        $response = ['result'=> 'success'];
        $idUsuarioSesion = $this->obtenerIdUsuarioSesion();
        if($this->sessionContainer && $this->dateFormat->isValid($fecha) && $idUsuarioSesion){
            if ($this->objAgendaVirtualTable->validarRangoAgendaVirtual($fecha)) {
                $fechaTiempoSelectedUsuario = date('Y-m-d H:i', strtotime(str_replace("/","-", $fecha)));
                $fechaTiempoFinalSelectedUsuario = date("Y-m-d H:i", strtotime($fechaTiempoSelectedUsuario . "+30 minutes"));
                $horariosDisponibles = $this->obtenerHorariosDisponibles($fecha, $idempresas);
                if($horariosDisponibles['usuario_fecha_disponible']){
                    //Guardar agenda del usuario
                    $dataAgenda = [
                        'idferias'=> $this->sessionContainer['idferias'],
                        'fecha_creacion'=> date('Y-m-d H:i:s'),
                        'idusuario'=> $idUsuarioSesion,
                        'tipo_usuario'=> $this->sessionContainer['tipo'],
                        'fecha_agenda_inicio'=> $fechaTiempoSelectedUsuario,
                        'fecha_agenda_fin'=> $fechaTiempoFinalSelectedUsuario,
                        'comentario'=> $comentario,
                        'idempresas'=> $idempresas
                    ];
                    $this->objAgendaVirtualTable->agregarAgendaVirtual($dataAgenda);
                    //Enviar correo al usuario
                    $correoPara = $this->sessionContainer['correo'];
                    $plantillaCorreo = "agenda-virtual";
                    //Obtener plantilla correos
                    $dataPlantillaCorreo = $this->objFeriasCorreosTable->obtenerDatosPlantillaCorreo($this->layout()->feria['idferias'], $plantillaCorreo);
                    if($dataPlantillaCorreo){
                        list($fechaUsuario, $horaUsuario) = explode(" ", $fecha);
                        $mailDatos = [
                            'informacion'=> [
                                'fecha'=> $fechaUsuario,
                                'hora'=> $horaUsuario,
                                'comentario'=> $comentario
                            ],
                            'contenidoCorreo'=> $dataPlantillaCorreo['contenidoCorreo'],
                            'visitante'=> $this->layout()->datosUsuario
                        ];
                        $this->objMailSender->sendMail($correoPara,$dataPlantillaCorreo['correoTitulo'],$mailDatos,$plantillaCorreo,null,null,$dataPlantillaCorreo['correoCopia'],$this->layout()->feria['nombre']);  
                    }
                } else {
                    $response = ['result'=> 'user-range-exist'];
                }
            } else {
                $response = ['result'=> 'not-range-date'];
            }
        }

        return $this->jsonZF($response);
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

    function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $validator = new DateFormat(['format' => 'Y']);
        $validator->isValid('2010'); // returns true
    }

    private function consoleZF($data){
        $viewModel = new ViewModel($data);
        $viewModel->setTerminal(true);
        return $viewModel;
    }

    private function jsonZF($data){
        return new JsonModel($data);
    }

}
