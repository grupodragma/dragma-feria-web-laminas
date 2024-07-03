<?php

namespace Application\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\Sql\Select;

class FeriasTable
{
    protected $tableGateway;
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    public function obtenerFerias($idferias, $idperfil)
    {
        $condiciones = [];
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT f.*, c.nombre as cliente, p.nombre as plan
        FROM ferias f
        LEFT JOIN clientes c ON c.idclientes = f.idclientes
        LEFT JOIN planes p ON p.idplanes = f.idplanes";
        if ($idperfil != 1 && $idferias != null) {
            $condiciones[] = "f.idferias = {$idferias}";
        }
        if (! empty($condiciones)) {
            $sql .= " WHERE " . implode(" AND ", $condiciones);
        }
        $sql .= " ORDER BY f.idferias DESC";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
    public function validarPlan($idferias)
    {
        $condiciones = [];
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT p.* FROM ferias f
        INNER JOIN planes AS p ON p.idplanes = f.idplanes
        WHERE f.idferias = {$idferias}";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->current();
        return $data;
    }
    public function obtenerDatosFerias($where)
    {
        $rowset = $this->tableGateway->select($where);
        return $rowset->toArray();
    }
    public function obtenerDatoFerias($where)
    {
        $rowset = $this->tableGateway->select($where);
        return $rowset->current();
    }
    public function actualizarDatosFerias($data, $idferias)
    {
        $rowset = $this->tableGateway->update($data, ["idferias" => $idferias]);
    }
    public function agregarFerias($data)
    {
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }
    public function eliminarFerias($idferias)
    {
        $this->tableGateway->delete(["idferias" => $idferias]);
    }
    public function obtenerFiltroDatosFerias($start, $length, $search = null, $totalregistro = false)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM ferias";
        $sql .= " ORDER BY idferias ASC";
        if (! $totalregistro) {
            $sql .= " LIMIT {$start},{$length}";
        }
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
    public function listarFeriasVisitantes($idferias, $fecha = null)
    {
        if ($fecha != null) {
            $fechas = explode(" - ", $fecha);
            $fecha_inicio = date('Y-m-d', strtotime(str_replace("/", "-", $fechas[0])));
            $fecha_fin = date('Y-m-d', strtotime(str_replace("/", "-", $fechas[1])));
        }
        $condiciones = [];
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT v.*, f.nombre AS feria
        FROM visitantes v
        LEFT JOIN ferias f ON f.idferias = v.idferias";
        if ($idferias != null) {
            $condiciones[] = "v.idferias = {$idferias}";
        }
        if ($fecha != null) {
            $condiciones[] = "v.fecha_registro_web >= '{$fecha_inicio} 00:00:00' AND v.fecha_registro_web <= '{$fecha_fin} 23:59:59'";
        }
        $condiciones[] = "v.fecha_registro_web IS NOT NULL";
        if (! empty($condiciones)) {
            $sql .= " WHERE " . implode(" AND ", $condiciones);
        }
        $sql .= " ORDER BY v.fecha_registro_web DESC";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
}
