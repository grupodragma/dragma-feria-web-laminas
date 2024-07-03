<?php

namespace Application\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\Sql\Select;

class VisitantesRegistrosTable
{
    protected $tableGateway;
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    public function obtenerVisitantesRegistros($idperfil = null, $idferias = null, $tiporegistro = null, $idusuarios = null, $tipo = null, $idusuariossesion = null)
    {
        $condiciones = [];
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT vr.idvisitantesregistros, vr.fecha_registro, IF(vr.tipo = 'P', 'PRESENCIAL', 'VIRTUAL') AS tipo_registro, f.nombre AS feria, CONCAT(u.nombres, ' ', u.apellido_paterno, ' ', u.apellido_materno) AS usuario, CONCAT(v.nombres, ' ', v.apellido_paterno, ' ', v.apellido_materno) AS visitante, vr.idusuario, v.numero_documento, v.correo, v.telefono
        FROM visitantes_registros vr
        INNER JOIN visitantes v ON v.idvisitantes = vr.idvisitantes
        INNER JOIN ferias f ON f.idferias = vr.idferias
        LEFT JOIN fd_usuarios u ON u.idusuario = vr.idusuario";
        if ($idperfil != "1" && $idferias != null) {
            $condiciones[] = "vr.idferias = '{$idferias}'";
        }
        if ($tiporegistro != null) {
            $condiciones[] = "vr.tipo = '{$tiporegistro}'";
        }
        if ($idusuarios != null) {
            $condiciones[] = "vr.idusuario = {$idusuarios}";
        }
        if ($idperfil != "1" && $idusuariossesion != null && $tipo == "R") {
            $condiciones[] = "vr.idusuario = {$idusuariossesion}";
        }
        if (! empty($condiciones)) {
            $sql .= " WHERE " . implode(" AND ", $condiciones);
        }
        //echo $sql;
        //die;
        $sql .= " ORDER BY vr.fecha_registro DESC";
        return $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
    }
    public function obtenerDatoVisitantesRegistros($where)
    {
        $rowset = $this->tableGateway->select($where);
        return $rowset->current();
    }
    public function obtenerDatosVisitantesRegistros($where)
    {
        $rowset = $this->tableGateway->select($where);
        return $rowset->toArray();
    }
    public function actualizarDatosVisitantesRegistros($data, $idvisitantesregistros)
    {
        $rowset = $this->tableGateway->update($data, ["idvisitantesregistros" => $idvisitantesregistros]);
    }
    public function agregarVisitantesRegistros($data)
    {
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }
    public function eliminarVisitantesRegistros($idvisitantesregistros)
    {
        $this->tableGateway->delete(["idvisitantesregistros" => $idvisitantesregistros]);
    }
    public function obtenerFiltroDatosVisitantesRegistros($start, $length, $search = null, $totalregistro = false)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM visitantesregistros";
        $sql .= " ORDER BY idvisitantesregistros ASC";
        if (! $totalregistro) {
            $sql .= " LIMIT {$start},{$length}";
        }
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
    public function filtrarUsuariosPorFeria($idperfil = null, $idferias = null)
    {
        $condiciones = [];
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT DISTINCT u.*, CONCAT(u.nombres, ' ', u.apellido_paterno, ' ', u.apellido_materno) AS usuario
        FROM visitantes_registros vr
        INNER JOIN fd_usuarios u ON u.idusuario = vr.idusuario";
        if ($idperfil != "1" && $idferias != null) {
            $condiciones[] = "vr.idferias = {$idferias}";
        }
        if (! empty($condiciones)) {
            $sql .= " WHERE " . implode(" AND ", $condiciones);
        }
        $sql .= " ORDER BY usuario ASC";
        return $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
    }
}
