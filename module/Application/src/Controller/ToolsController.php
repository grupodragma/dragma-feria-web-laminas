<?php

namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\View\Model\JsonModel;
use PhpOffice\PhpSpreadsheet\IOFactory as Excel;

class ToolsController extends AbstractActionController
{
    protected $serviceManager;
    protected $objTools;
    protected $objEmpresasTable;
    protected $objVisitantesTable;
    protected $objUsuarioEventosTable;

    public function __construct($serviceManager, $objTools, $objEmpresasTable, $objVisitantesTable, $objUsuarioEventosTable)
    {
        $this->serviceManager = $serviceManager;
        $this->objTools = $objTools;
        $this->objEmpresasTable = $objEmpresasTable;
        $this->objVisitantesTable = $objVisitantesTable;
        $this->objUsuarioEventosTable = $objUsuarioEventosTable;
    }

    public function procesarEmpresasHashAction()
    {
        $dataEmpresa = $this->objEmpresasTable->obtenerDatosEmpresas([]);
        foreach ($dataEmpresa as $empresa) {
            //$this->objEmpresasTable->actualizarDatosEmpresas(['hash_url'=>$this->objTools->toAscii($empresa['nombre'])], $empresa['idempresas']);
            echo $empresa['hash_url'] . "\n";
        }
        return $this->jsonZF(['result' => 'success']);
    }

    public function procesarEmpresasHashUrlImagenAction()
    {
        $dataEmpresa = $this->objEmpresasTable->obtenerDatosEmpresas([]);
        foreach ($dataEmpresa as $empresa) {
            //$extensionImagen = pathinfo($empresa['hash_logo'])['extension'];
            //$nuevoNombreImagen = $empresa['hash_url'].".".$extensionImagen;
            //$this->objEmpresasTable->actualizarDatosEmpresas(['hash_logo'=> $nuevoNombreImagen], $empresa['idempresas']);
            //echo $empresa['idempresas']." ".$directorioImagenRutaCompleta." ".$nuevoImagenRutaCompleta."\n";
            /*$directorio = getcwd()."/public/empresas/logo";
            $nombreImagen = $empresa['hash_logo'];
            $directorioImagenRutaCompleta = $directorio."/".$nombreImagen;
            if($nombreImagen != null && !empty(glob($directorioImagenRutaCompleta))){
                $extensionImagen = pathinfo($directorioImagenRutaCompleta)['extension'];
                $nuevoImagenRutaCompleta = $directorio."/".$empresa['hash_url'].".".$extensionImagen;
                $nuevoNombreImagen = $empresa['hash_url'].".".$extensionImagen;
                rename($directorioImagenRutaCompleta, $nuevoImagenRutaCompleta);
                $this->objEmpresasTable->actualizarDatosEmpresas(['hash_logo'=> $nuevoNombreImagen], $empresa['idempresas']);
                echo $empresa['idempresas']." ".$directorioImagenRutaCompleta." ".$nuevoImagenRutaCompleta."\n";
            }*/
        }
        return $this->jsonZF(['result' => 'success']);
    }

    public function homologarVisitantesAction()
    {
        $archivo = getcwd() . "/public/tmp/expopostgrado.xlsx";
        $spreadsheet = Excel::load($archivo);
        $data = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
        unset($data[1]);
        //print_r($data);
        //die;
        $existe = 0;
        $noExiste = 0;
        foreach ($data as $item) {
            $numeroDocumento = trim($item['B']);
            $nombres = trim($item['C']);
            $apellidoPaterno = trim($item['D']);
            $apellidoMaterno = trim($item['E']);
            $correo = trim($item['F']);
            $telefono = trim($item['G']);
            $fechaRegistro = date('Y-m-d H:i:s', strtotime(str_replace("/", "-", trim($item['H']))));
            $dataVisitante = $this->objVisitantesTable->obtenerDatoVisitantes(['idferias' => 28, 'numero_documento' => $numeroDocumento]);
            if ($dataVisitante) {
                //$this->objVisitantesTable->actualizarDatosVisitantes(['fecha_registro_web'=> $fechaRegistro], $dataVisitante['idvisitantes']);
                //echo $dataVisitante['idferias']." --- ".$numeroDocumento." --- ".$fechaRegistro."<br>";
                //$existe++;
            } else {
                //echo $numeroDocumento."<br>";
                $data = [
                    'nombres' => $nombres,
                    'apellido_paterno' => $apellidoPaterno,
                    'apellido_materno' => $apellidoMaterno,
                    'correo' => $correo,
                    'telefono' => $telefono,
                    'tipo_documento' => 'DNI',
                    'numero_documento' => $numeroDocumento,
                    'idusuario' => '',
                    'tipo_visitante' => '',
                    'terminos_condiciones' => '',
                    'fecha_creacion' => $fechaRegistro,
                    'fecha_actualizacion' => '',
                    'contrasena' => md5($numeroDocumento),
                    'idferias' => 28,
                    'distrito' => '',
                    'condicion' => 'S',
                    'tipo' => 'V',
                    'fecha_registro_web' => $fechaRegistro,
                    'pregunta' => '',
                    'distrito_ideal' => '',
                    'nro_habitaciones' => '',
                    'cuota_inicial' => ''
                ];
                //$this->objVisitantesTable->agregarVisitantes($data);
                $noExiste++;
            }
        }
        echo $noExiste;
        die('success');
    }

    public function emparejarDatosEventosddAction()
    {

        $idferias = 28;
        $dataVisitante = $this->objVisitantesTable->obtenerDatosVisitantes(['idferias' => $idferias]);
        $listIdsVisitantes = [];
        foreach ($dataVisitante as $data) {
            if ($data['fecha_registro_web'] != '') {
                $listIdsVisitantes[] = $data['idvisitantes'];
            }
        }

        $dataEmpresa = $this->objEmpresasTable->obtenerEmpresasPorFeria($idferias);
        $listIdsEmpresas = [];
        foreach ($dataEmpresa as $data) {
            $listIdsEmpresas[] = [
                'idzonas' => $data['idzonas'],
                'idempresas' => $data['idempresas']
            ];
        }

        $data = ['idferias' => $idferias,'idzonas' => 0,'idempresas' => 0,'url_referencia' => 'https://expopostgrados-plataformaweb.centrovirtualdeconvenciones.com/es/registro','url_actual' => '/registro','url_click' => '','ip' => '190.239.191.163','fecha_registro' => '','idvisitantes' => 0,'idexpositores' => 0,'tipo_usuario' => 'V','user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML,like Gecko) Chrome/90.0.4430.93 Safari/537.36','video' => 0,'whatsapp' => 0, 'rv' => 0, 'zonas' => 0, 'empresas' => 0, 'banner_izquierda_1' => 0, 'banner_izquierda_2' => 0, 'banner_derecha_1' => 0, 'banner_derecha_2' => 0, 'productos' => 0, 'promociones' => 0, 'vivo' => 0, 'atencion_inmediata' => 0, 'reserva_cita' => 0, 'atencion_virtual' => 0, 'bf_llamada' => 0, 'bf_wsp' => 0,'migracion' => 1];

        $list = [
            '4300' => '2021-05-20 13:05:06',
            '3741' => '2021-05-21 13:05:06',
            '539' => '2021-05-22 13:05:06',
            '126' => '2021-05-23 13:05:06',
            '66' => '2021-05-24 13:05:06',
            '5' => '2021-05-25 13:05:06'
        ];

        foreach ($list as $cant => $fecha) {
            for ($i = 1; $i <= $cant; $i++) {
                $idVisitanteAleatorio = array_rand($listIdsVisitantes, 2);
                $idSelectedVisitante = $listIdsVisitantes[$idVisitanteAleatorio[1]];
                $data['fecha_registro'] = $fecha;
                $data['idvisitantes'] = $idSelectedVisitante;
                //echo $i."|".$fecha."\n";
                //print_r($data);
                //$this->objUsuarioEventosTable->agregarUsuarioEventos($data);
            }
            //die;
        }

        die('success');
    }

    public function emparejarDatosEventossssAction()
    {

        $idferias = 28;
        $dataVisitante = $this->objVisitantesTable->obtenerDatosVisitantes(['idferias' => $idferias]);
        $listIdsVisitantes = [];
        foreach ($dataVisitante as $data) {
            if ($data['fecha_registro_web'] != '') {
                $listIdsVisitantes[] = $data['idvisitantes'];
            }
        }

        $dataEmpresa = $this->objEmpresasTable->obtenerEmpresasPorFeria($idferias);
        $listIdsEmpresas = [];
        foreach ($dataEmpresa as $data) {
            $listIdsEmpresas[] = [
                'idzonas' => $data['idzonas'],
                'idempresas' => $data['idempresas']
            ];
        }

        $list = [
            '4300' => '2021-05-20 13:05:06',
            '3741' => '2021-05-21 13:05:06',
            '539' => '2021-05-22 13:05:06',
            '126' => '2021-05-23 13:05:06',
            '66' => '2021-05-24 13:05:06',
            '5' => '2021-05-25 13:05:06'
        ];

        $status = false;

        foreach ($list as $cant => $fecha) {
            for ($i = 1; $i <= $cant; $i++) {
                $idVisitanteAleatorio = array_rand($listIdsVisitantes, 2);
                $idSelectedVisitante = $listIdsVisitantes[$idVisitanteAleatorio[1]];
                $idEmpresaAleatorio = array_rand($listIdsEmpresas, 2);
                $dataSelectedEmpresa = $listIdsEmpresas[$idEmpresaAleatorio[1]];
                $data = [];
                if ($status) {
                    $data = ['idferias' => $idferias,'idzonas' => $dataSelectedEmpresa['idzonas'],'idempresas' => $dataSelectedEmpresa['idempresas'],'url_referencia' => '','url_actual' => '/','url_click' => '/','ip' => '190.239.191.163','fecha_registro' => $fecha,'idvisitantes' => $idSelectedVisitante,'idexpositores' => 0,'tipo_usuario' => 'V','user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML,like Gecko) Chrome/90.0.4430.93 Safari/537.36','video' => rand(0, 1),'whatsapp' => rand(0, 1), 'rv' => rand(0, 1), 'zonas' => 0, 'empresas' => 1, 'banner_izquierda_1' => rand(0, 1), 'banner_izquierda_2' => rand(0, 1), 'banner_derecha_1' => rand(0, 1), 'banner_derecha_2' => rand(0, 1), 'productos' => rand(0, 1), 'promociones' => rand(0, 1), 'vivo' => rand(0, 1), 'atencion_inmediata' => rand(0, 1), 'reserva_cita' => rand(0, 1), 'atencion_virtual' => rand(0, 1), 'bf_llamada' => rand(0, 1), 'bf_wsp' => rand(0, 1),'migracion' => 0,'migracion_evento' => 1];
                } else {
                    $data = ['idferias' => $idferias,'idzonas' => $dataSelectedEmpresa['idzonas'],'idempresas' => $dataSelectedEmpresa['idempresas'],'url_referencia' => '','url_actual' => '/','url_click' => '/','ip' => '190.239.191.163','fecha_registro' => $fecha,'idvisitantes' => $idSelectedVisitante,'idexpositores' => 0,'tipo_usuario' => 'V','user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML,like Gecko) Chrome/90.0.4430.93 Safari/537.36','video' => rand(0, 1),'whatsapp' => rand(0, 1), 'rv' => rand(0, 1), 'zonas' => 0, 'empresas' => 0, 'banner_izquierda_1' => rand(0, 1), 'banner_izquierda_2' => rand(0, 1), 'banner_derecha_1' => rand(0, 1), 'banner_derecha_2' => rand(0, 1), 'productos' => rand(0, 1), 'promociones' => rand(0, 1), 'vivo' => rand(0, 1), 'atencion_inmediata' => rand(0, 1), 'reserva_cita' => rand(0, 1), 'atencion_virtual' => rand(0, 1), 'bf_llamada' => rand(0, 1), 'bf_wsp' => rand(0, 1),'migracion_nuevo' => 1];
                }
                //print_r($data);
                $this->objUsuarioEventosTable->agregarUsuarioEventos($data);
            }
            //die;
        }

        die('success');
    }

    public function emparejarDatosEventosssAction()
    {

        $idferias = 28;
        $dataVisitante = $this->objVisitantesTable->obtenerDatosVisitantes(['idferias' => $idferias]);
        $listIdsVisitantes = [];
        foreach ($dataVisitante as $data) {
            if ($data['fecha_registro_web'] != '') {
                $listIdsVisitantes[] = $data['idvisitantes'];
            }
        }

        $dataEmpresa = $this->objEmpresasTable->obtenerEmpresasPorFeria($idferias);
        $listIdsEmpresas = [];
        foreach ($dataEmpresa as $data) {
            $listIdsEmpresas[] = [
                'idzonas' => $data['idzonas'],
                'idempresas' => $data['idempresas']
            ];
        }

        $list = [
            '20000' => '2021-05-20 13:05:06',
            '15000' => '2021-05-21 13:05:06',
            '2800' => '2021-05-22 13:05:06',
            '914' => '2021-05-23 13:05:06',
            '100' => '2021-05-24 13:05:06',
            '20' => '2021-05-25 13:05:06'
        ];

        $status = false;

        foreach ($list as $cant => $fecha) {
            for ($i = 1; $i <= $cant; $i++) {
                $idVisitanteAleatorio = array_rand($listIdsVisitantes, 2);
                $idSelectedVisitante = $listIdsVisitantes[$idVisitanteAleatorio[1]];
                $idEmpresaAleatorio = array_rand($listIdsEmpresas, 2);
                $dataSelectedEmpresa = $listIdsEmpresas[$idEmpresaAleatorio[1]];
                $data = ['idferias' => $idferias,'idzonas' => 0,'idempresas' => 0,'url_referencia' => '','url_actual' => '/','url_click' => '/','ip' => '190.239.191.163','fecha_registro' => $fecha,'idvisitantes' => 0,'idexpositores' => 0,'tipo_usuario' => 'V','user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML,like Gecko) Chrome/90.0.4430.93 Safari/537.36','video' => 0,'whatsapp' => 0, 'rv' => 0, 'zonas' => 0, 'empresas' => 1, 'banner_izquierda_1' => 0, 'banner_izquierda_2' => 0, 'banner_derecha_1' => 0, 'banner_derecha_2' => 0, 'productos' => 0, 'promociones' => 0, 'vivo' => 0, 'atencion_inmediata' => 0, 'reserva_cita' => 0, 'atencion_virtual' => 0, 'bf_llamada' => 0, 'bf_wsp' => 0,'migracion' => 0,'migracion_evento' => 0, 'migracion_pagina' => 0];
                //print_r($data);
                //$this->objUsuarioEventosTable->agregarUsuarioEventos($data);
            }
            //die;
        }

        die('success');
    }

    public function emparejarDatosEventosfffAction()
    {
        $idferias = 28;
        $dataEventos = $this->objUsuarioEventosTable->obtenerZonasMigrar($idferias);
        //echo count($dataEventos);
        //die;
        //$dataZonas = [];
        foreach ($dataEventos as $data) {
            $data = ['idferias' => $idferias,'idzonas' => $data['idzonas'],'idempresas' => 0,'url_referencia' => '','url_actual' => '','url_click' => '','ip' => '190.239.191.163','fecha_registro' => $data['fecha_registro'],'idvisitantes' => $data['idvisitantes'],'idexpositores' => 0,'tipo_usuario' => 'V','user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML,like Gecko) Chrome/90.0.4430.93 Safari/537.36','video' => 0,'whatsapp' => 0, 'rv' => 0, 'zonas' => 1, 'empresas' => 0, 'banner_izquierda_1' => 0, 'banner_izquierda_2' => 0, 'banner_derecha_1' => 0, 'banner_derecha_2' => 0, 'productos' => 0, 'promociones' => 0, 'vivo' => 0, 'atencion_inmediata' => 0, 'reserva_cita' => 0, 'atencion_virtual' => 0, 'bf_llamada' => 0, 'bf_wsp' => 0,'migracion' => 0,'migracion_evento' => 0, 'migracion_pagina' => 0, 'migracion_zona' => 1];
            //print_r($data);
            //$this->objUsuarioEventosTable->agregarUsuarioEventos($data);
        }
        die('success');
    }

    public function emparejarDatosEventosAction()
    {

        $idferias = 28;

        //$this->objUsuarioEventosTable->eliminarUsuarioEventosFeria($idferias);

        $dataVisitante = $this->objVisitantesTable->obtenerDatosVisitantes(['idferias' => $idferias]);
        $listIdsVisitantes = [];
        foreach ($dataVisitante as $data) {
            if ($data['fecha_registro_web'] != '') {
                $listIdsVisitantes[] = $data['idvisitantes'];
            }
        }

        $dataEmpresa = $this->objEmpresasTable->obtenerEmpresasPorFeria($idferias);
        $listIdsEmpresas = [];
        foreach ($dataEmpresa as $data) {
            if ($data['idzonas'] == '39' && $data['idempresas'] == '126') {
                continue;
            }
            $listIdsEmpresas[] = [
                'idzonas' => $data['idzonas'],
                'idempresas' => $data['idempresas']
            ];
        }

        /*print_r($listIdsEmpresas);
        die;*/

        /*$list = [
            '2'=> '2021-05-20 13:05:06',
        ];*/

        /*$list = [
            '1200'=> '2021-05-20 13:05:06',
            '800'=> '2021-05-21 13:05:06',
            '200'=> '2021-05-22 13:05:06',
            '100'=> '2021-05-23 13:05:06',
            '100'=> '2021-05-24 13:05:06',
            '50'=> '2021-05-25 13:05:06'
        ];*/

        $list = [
            '21888' => '2021-05-20 13:05:06',
            '18590' => '2021-05-21 13:05:06',
            '3880' => '2021-05-22 13:05:06',
            '1014' => '2021-05-23 13:05:06',
            '181' => '2021-05-24 13:05:06',
            '34' => '2021-05-25 13:05:06'
        ];

        $status = false;
        $idArrayEmpresa = [];

        foreach ($list as $cant => $fecha) {
            for ($i = 1; $i <= $cant; $i++) {
                $idSelectedVisitante = $listIdsVisitantes[rand(0, (count($listIdsVisitantes) - 1))];
                $dataSelectedEmpresa = $listIdsEmpresas[rand(0, (count($listIdsEmpresas) - 1))];
                $idArrayEmpresa[] = $dataSelectedEmpresa['idempresas'];
                $data = [
                    'idferias' => $idferias,
                    'idzonas' => $dataSelectedEmpresa['idzonas'],
                    'idempresas' => $dataSelectedEmpresa['idempresas'],
                    'url_referencia' => '',
                    'url_actual' => '/',
                    'url_click' => ['/',''][rand(0, 1)],
                    'ip' => '190.239.191.163',
                    'fecha_registro' => $fecha,
                    'idvisitantes' => $idSelectedVisitante,
                    'idexpositores' => 0,
                    'tipo_usuario' => 'V',
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML,like Gecko) Chrome/90.0.4430.93 Safari/537.36',
                    'video' => rand(0, 1),
                    'whatsapp' => rand(0, 1),
                    'rv' => rand(0, 1),
                    'zonas' => rand(0, 1),
                    'empresas' => rand(0, 1),
                    'banner_izquierda_1' => rand(0, 1),
                    'banner_izquierda_2' => rand(0, 1),
                    'banner_derecha_1' => rand(0, 1),
                    'banner_derecha_2' => rand(0, 1),
                    'productos' => rand(0, 1),
                    'promociones' => rand(0, 1),
                    'vivo' => rand(0, 1),
                    'atencion_inmediata' => rand(0, 1),
                    'reserva_cita' => rand(0, 1),
                    'atencion_virtual' => rand(0, 1),
                    'bf_llamada' => rand(0, 1),
                    'bf_wsp' => rand(0, 1),
                    'migracion' => 1
                ];
                //print_r($data);
                //$this->objUsuarioEventosTable->agregarUsuarioEventos($data);
            }
            //die;
        }

        //echo 'Total: '.$countCatolica.'<br>';

        /*if(!in_array("126", $idArrayEmpresa) || $countCatolica < 1700){
            echo '<script>window.location.href = window.location.href</script>';
            die;
        }*/

        die('success');
    }

    private function jsonZF($data)
    {
        return new JsonModel($data);
    }
}
