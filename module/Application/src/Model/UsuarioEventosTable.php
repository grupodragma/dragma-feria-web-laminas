<?php

namespace Application\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\Sql\Select;

class UsuarioEventosTable
{
    protected $tableGateway;
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    public function listarEmpresasAgrupados($idferias = null, $fecha = null)
    {
        if ($fecha != null) {
            $fechas = explode(" - ", $fecha);
            $fecha_inicio = date('Y-m-d', strtotime(str_replace("/", "-", $fechas[0])));
            $fecha_fin = date('Y-m-d', strtotime(str_replace("/", "-", $fechas[1])));
        }
        $condiciones = [];
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT ue.*, COUNT(*) AS total_nro_sesiones, e.nombre AS empresa
        FROM usuario_eventos ue
        LEFT JOIN empresas e ON e.idempresas = ue.idempresas";
        if ($fecha != null) {
            $condiciones[] = "ue.fecha_registro >= '{$fecha_inicio} 00:00:00' AND ue.fecha_registro <= '{$fecha_fin} 23:59:59'";
        }
        if ($idferias != null) {
            $condiciones[] = "ue.idferias = {$idferias}";
        }
        $condiciones[] = "ue.empresas = 1";
        $condiciones[] = "ue.zonas != 1";
        $condiciones[] = "ue.idzonas != 0";
        $condiciones[] = "ue.idempresas != 0";
        $condiciones[] = "ue.idvisitantes != 0";
        $condiciones[] = "ue.idexpositores = 0";
        if (! empty($condiciones)) {
            $sql .= " WHERE " . implode(" AND ", $condiciones);
        }
        $sql .= " GROUP BY ue.idempresas";
        $sql .= " ORDER BY total_nro_sesiones DESC";
        //echo $sql;
        //die;
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
    public function obtenerDataEventosEmpresas($idferias = null, $idempresas = null, $fecha = null)
    {
        if ($fecha != null) {
            $fechas = explode(" - ", $fecha);
            $fecha_inicio = date('Y-m-d', strtotime(str_replace("/", "-", $fechas[0])));
            $fecha_fin = date('Y-m-d', strtotime(str_replace("/", "-", $fechas[1])));
        }
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT v.*, 
            IF((SELECT COUNT(*) FROM usuario_eventos WHERE video = 1 AND idferias = {$idferias} AND idempresas = {$idempresas} AND empresas != 1 AND zonas != 1 AND idzonas != 0 AND idempresas != 0 AND url_click != '' AND idvisitantes = v.idvisitantes AND idexpositores = 0), 1, 0) AS video,
            IF((SELECT COUNT(*) FROM usuario_eventos WHERE whatsapp = 1 AND idferias = {$idferias} AND idempresas = {$idempresas} AND empresas != 1 AND zonas != 1 AND idzonas != 0 AND idempresas != 0 AND url_click != '' AND idvisitantes = v.idvisitantes AND idexpositores = 0), 1, 0) AS whatsapp,
            IF((SELECT COUNT(*) FROM usuario_eventos WHERE rv = 1 AND idferias = {$idferias} AND idempresas = {$idempresas} AND empresas != 1 AND zonas != 1 AND idzonas != 0 AND idempresas != 0 AND url_click != '' AND idvisitantes = v.idvisitantes AND idexpositores = 0), 1, 0) AS rv,
            IF((SELECT COUNT(*) FROM usuario_eventos WHERE productos = 1 AND idferias = {$idferias} AND idempresas = {$idempresas} AND empresas != 1 AND zonas != 1 AND idzonas != 0 AND idempresas != 0 AND url_click != '' AND idvisitantes = v.idvisitantes AND idexpositores = 0), 1, 0) AS productos,
            IF((SELECT COUNT(*) FROM usuario_eventos WHERE promociones = 1 AND idferias = {$idferias} AND idempresas = {$idempresas} AND empresas != 1 AND zonas != 1 AND idzonas != 0 AND idempresas != 0 AND url_click != '' AND idvisitantes = v.idvisitantes AND idexpositores = 0), 1, 0) AS promociones,
            IF((SELECT COUNT(*) FROM usuario_eventos WHERE vivo = 1 AND idferias = {$idferias} AND idempresas = {$idempresas} AND empresas != 1 AND zonas != 1 AND idzonas != 0 AND idempresas != 0 AND url_click != '' AND idvisitantes = v.idvisitantes AND idexpositores = 0), 1, 0) AS vivo,
            IF((SELECT COUNT(*) FROM usuario_eventos WHERE banner_izquierda_1 = 1 AND idferias = {$idferias} AND idempresas = {$idempresas} AND empresas != 1 AND zonas != 1 AND idzonas != 0 AND idempresas != 0 AND url_click = '' AND idvisitantes = v.idvisitantes AND idexpositores = 0), 1, 0) AS banner_izquierda_1,
            IF((SELECT COUNT(*) FROM usuario_eventos WHERE banner_izquierda_2 = 1 AND idferias = {$idferias} AND idempresas = {$idempresas} AND empresas != 1 AND zonas != 1 AND idzonas != 0 AND idempresas != 0 AND url_click = '' AND idvisitantes = v.idvisitantes AND idexpositores = 0), 1, 0) AS banner_izquierda_2,
            IF((SELECT COUNT(*) FROM usuario_eventos WHERE banner_derecha_1 = 1 AND idferias = {$idferias} AND idempresas = {$idempresas} AND empresas != 1 AND zonas != 1 AND idzonas != 0 AND idempresas != 0 AND url_click = '' AND idvisitantes = v.idvisitantes AND idexpositores = 0), 1, 0) AS banner_derecha_1,
            IF((SELECT COUNT(*) FROM usuario_eventos WHERE banner_derecha_2 = 1 AND idferias = {$idferias} AND idempresas = {$idempresas} AND empresas != 1 AND zonas != 1 AND idzonas != 0 AND idempresas != 0 AND url_click = '' AND idvisitantes = v.idvisitantes AND idexpositores = 0), 1, 0) AS banner_derecha_2,
            IF((SELECT COUNT(*) FROM usuario_eventos WHERE reserva_cita = 1 AND idferias = {$idferias} AND idempresas = {$idempresas} AND empresas != 1 AND zonas != 1 AND idzonas != 0 AND idempresas != 0 AND url_click = '' AND idvisitantes = v.idvisitantes AND idexpositores = 0), 1, 0) AS reserva_cita,
            IF((SELECT COUNT(*) FROM usuario_eventos WHERE bf_llamada = 1 AND idferias = {$idferias} AND idempresas = {$idempresas} AND empresas != 1 AND zonas != 1 AND idzonas != 0 AND idempresas != 0 AND idvisitantes = v.idvisitantes AND idexpositores = 0), 1, 0) AS bf_llamada,
            IF((SELECT COUNT(*) FROM usuario_eventos WHERE bf_wsp = 1 AND idferias = {$idferias} AND idempresas = {$idempresas} AND empresas != 1 AND zonas != 1 AND idzonas != 0 AND idempresas != 0 AND idvisitantes = v.idvisitantes AND idexpositores = 0), 1, 0) AS bf_wsp,
            IF((SELECT COUNT(*) FROM usuario_eventos WHERE productos_recorrido360 = 1 AND idferias = {$idferias} AND idempresas = {$idempresas} AND empresas != 1 AND zonas != 1 AND idzonas != 0 AND idempresas != 0 AND idvisitantes = v.idvisitantes AND idexpositores = 0), 1, 0) AS productos_recorrido360,
            IF((SELECT COUNT(*) FROM usuario_eventos WHERE productos_mapa = 1 AND idferias = {$idferias} AND idempresas = {$idempresas} AND empresas != 1 AND zonas != 1 AND idzonas != 0 AND idempresas != 0 AND idvisitantes = v.idvisitantes AND idexpositores = 0), 1, 0) AS productos_mapa,
            IF((SELECT COUNT(*) FROM usuario_eventos WHERE productos_brochure = 1 AND idferias = {$idferias} AND idempresas = {$idempresas} AND empresas != 1 AND zonas != 1 AND idzonas != 0 AND idempresas != 0 AND idvisitantes = v.idvisitantes AND idexpositores = 0), 1, 0) AS productos_brochure,
            IF((SELECT COUNT(*) FROM usuario_eventos WHERE productos_wsp = 1 AND idferias = {$idferias} AND idempresas = {$idempresas} AND empresas != 1 AND zonas != 1 AND idzonas != 0 AND idempresas != 0 AND idvisitantes = v.idvisitantes AND idexpositores = 0), 1, 0) AS productos_wsp,
            IF((SELECT COUNT(*) FROM usuario_eventos WHERE planos_wsp = 1 AND idferias = {$idferias} AND idempresas = {$idempresas} AND empresas != 1 AND zonas != 1 AND idzonas != 0 AND idempresas != 0 AND idvisitantes = v.idvisitantes AND idexpositores = 0), 1, 0) AS planos_wsp,
            IF((SELECT COUNT(*) FROM usuario_eventos WHERE planos_solicitar_informacion = 1 AND idferias = {$idferias} AND idempresas = {$idempresas} AND empresas != 1 AND zonas != 1 AND idzonas != 0 AND idempresas != 0 AND idvisitantes = v.idvisitantes AND idexpositores = 0), 1, 0) AS planos_solicitar_informacion,
            IF((SELECT COUNT(*) FROM usuario_eventos WHERE planos_url = 1 AND idferias = {$idferias} AND idempresas = {$idempresas} AND empresas != 1 AND zonas != 1 AND idzonas != 0 AND idempresas != 0 AND idvisitantes = v.idvisitantes AND idexpositores = 0), 1, 0) AS planos_url,
            IF((SELECT COUNT(*) FROM usuario_eventos WHERE promociones_modal_abrir_enlace = 1 AND idferias = {$idferias} AND idempresas = {$idempresas} AND empresas != 1 AND zonas != 1 AND idzonas != 0 AND idempresas != 0 AND idvisitantes = v.idvisitantes AND idexpositores = 0), 1, 0) AS promociones_modal_abrir_enlace,
            IF((SELECT COUNT(*) FROM usuario_eventos WHERE promociones_modal_envia_correo = 1 AND idferias = {$idferias} AND idempresas = {$idempresas} AND empresas != 1 AND zonas != 1 AND idzonas != 0 AND idempresas != 0 AND idvisitantes = v.idvisitantes AND idexpositores = 0), 1, 0) AS promociones_modal_envia_correo,
            IF((SELECT COUNT(*) FROM usuario_eventos WHERE promociones_modal_wsp = 1 AND idferias = {$idferias} AND idempresas = {$idempresas} AND empresas != 1 AND zonas != 1 AND idzonas != 0 AND idempresas != 0 AND idvisitantes = v.idvisitantes AND idexpositores = 0), 1, 0) AS promociones_modal_wsp,
            IF((SELECT COUNT(*) FROM usuario_eventos WHERE promociones_modal_brochure = 1 AND idferias = {$idferias} AND idempresas = {$idempresas} AND empresas != 1 AND zonas != 1 AND idzonas != 0 AND idempresas != 0 AND idvisitantes = v.idvisitantes AND idexpositores = 0), 1, 0) AS promociones_modal_brochure,
            IF((SELECT COUNT(*) FROM usuario_eventos WHERE expositores_vivo_corrreo = 1 AND idferias = {$idferias} AND idempresas = {$idempresas} AND empresas != 1 AND zonas != 1 AND idzonas != 0 AND idempresas != 0 AND idvisitantes = v.idvisitantes AND idexpositores = 0), 1, 0) AS expositores_vivo_corrreo,
            IF((SELECT COUNT(*) FROM usuario_eventos WHERE expositores_vivo_telefono = 1 AND idferias = {$idferias} AND idempresas = {$idempresas} AND empresas != 1 AND zonas != 1 AND idzonas != 0 AND idempresas != 0 AND idvisitantes = v.idvisitantes AND idexpositores = 0), 1, 0) AS expositores_vivo_telefono,
            IF((SELECT COUNT(*) FROM usuario_eventos WHERE expositores_vivo_wsp = 1 AND idferias = {$idferias} AND idempresas = {$idempresas} AND empresas != 1 AND zonas != 1 AND idzonas != 0 AND idempresas != 0 AND idvisitantes = v.idvisitantes AND idexpositores = 0), 1, 0) AS expositores_vivo_wsp
        FROM (
            SELECT ue.*, e.nombre AS empresa, z.nombre AS zona, CONCAT(IFNULL(v.nombres, ''),' ', IFNULL(v.apellido_paterno, ''),' ', IFNULL(v.apellido_materno, '')) AS visitante, v.numero_documento, v.correo, v.telefono, v.celular, v.registro_base, COUNT(*) AS total_visitas
            FROM usuario_eventos ue
            LEFT JOIN empresas e ON e.idempresas = ue.idempresas
            LEFT JOIN zonas z ON z .idzonas = ue.idzonas
            INNER JOIN visitantes v ON v.idvisitantes = ue.idvisitantes
            WHERE ue.idferias = {$idferias} AND ue.idempresas = {$idempresas} AND ue.empresas != 1 AND ue.zonas != 1 AND ue.idzonas != 0 AND ue.idempresas != 0 AND ue.url_click != '' AND ue.idvisitantes != 0 AND ue.idexpositores = 0 AND ue.fecha_registro >= '{$fecha_inicio} 00:00:00' AND ue.fecha_registro <= '{$fecha_fin} 23:59:59'
            GROUP BY ue.idvisitantes
            ORDER BY ue.idvisitantes, ue.fecha_registro DESC
        ) AS v";
        // echo $sql;
        // die;
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
    public function obtenerDatoUsuarioEventos($where)
    {
        $rowset = $this->tableGateway->select($where);
        return $rowset->current();
    }
    public function obtenerDatosUsuarioEventos($where)
    {
        $rowset = $this->tableGateway->select($where);
        return $rowset->toArray();
    }
    public function actualizarDatosUsuarioEventos($data, $idusuarioeventos)
    {
        $rowset = $this->tableGateway->update($data, ["idusuarioeventos" => $idusuarioeventos]);
    }
    public function agregarUsuarioEventos($data)
    {
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }
    public function eliminarUsuarioEventosFeria($idferias)
    {
        $this->tableGateway->delete(["idferias" => $idferias]);
    }
    public function eliminarUsuarioEventos($idusuarioeventos)
    {
        $this->tableGateway->delete(["idusuarioeventos" => $idusuarioeventos]);
    }
    public function totalSesionesRealizadas($idferias = null, $fecha = null)
    {
        if ($fecha != null) {
            $fechas = explode(" - ", $fecha);
            $fecha_inicio = date('Y-m-d', strtotime(str_replace("/", "-", $fechas[0])));
            $fecha_fin = date('Y-m-d', strtotime(str_replace("/", "-", $fechas[1])));
        }
        $condiciones = [];
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT COUNT(*) AS total
        FROM usuario_eventos ue
        INNER JOIN visitantes v ON v.idvisitantes = ue.idvisitantes";
        $condiciones[] = "ue.idvisitantes != 0";
        $condiciones[] = "ue.tipo_usuario = 'V'";
        $condiciones[] = "ue.idferias = {$idferias}";
        $condiciones[] = "v.idferias = {$idferias}";
        if ($fecha != null) {
            $condiciones[] = "fecha_registro >= '{$fecha_inicio} 00:00:00' AND fecha_registro <= '{$fecha_fin} 23:59:59'";
        }
        if (! empty($condiciones)) {
            $sql .= " WHERE " . implode(" AND ", $condiciones);
        }
        //echo $sql;
        //die;
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->current();
        return (int)$data['total'];
    }
    public function totalPaginasVisitadas($idferias = null, $fecha = null)
    {
        if ($fecha != null) {
            $fechas = explode(" - ", $fecha);
            $fecha_inicio = date('Y-m-d', strtotime(str_replace("/", "-", $fechas[0])));
            $fecha_fin = date('Y-m-d', strtotime(str_replace("/", "-", $fechas[1])));
        }
        $condiciones = [];
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT COUNT(*) AS total
        FROM usuario_eventos";
        $condiciones[] = "idferias = {$idferias}";
        $condiciones[] = "url_click != ''";
        if ($fecha != null) {
            $condiciones[] = "fecha_registro >= '{$fecha_inicio} 00:00:00' AND fecha_registro <= '{$fecha_fin} 23:59:59'";
        }
        if (! empty($condiciones)) {
            $sql .= " WHERE " . implode(" AND ", $condiciones);
        }
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->current();
        return (int)$data['total'];
    }

    public function obtenerZonasMigrar($idferias)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM usuario_eventos WHERE idferias = {$idferias} and idzonas != 0 AND fecha_registro >= '2021-05-20 00:00:00' AND fecha_registro <= '2021-05-25 23:59:59'";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
}
