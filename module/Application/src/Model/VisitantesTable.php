<?php

namespace Application\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\Sql\Select;

class VisitantesTable
{
    protected $tableGateway;
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    public function obtenerVisitantes()
    {
        $select = $this->tableGateway->getSql()->select();
        $select->order('idvisitantes DESC');
        $rowset = $this->tableGateway->selectWith($select);
        return $rowset->toArray();
    }
    public function obtenerDatoVisitantes($where)
    {
        $rowset = $this->tableGateway->select($where);
        return $rowset->current();
    }
    public function obtenerDatosVisitantes($where)
    {
        $rowset = $this->tableGateway->select($where);
        return $rowset->toArray();
    }
    public function actualizarDatosVisitantes($data, $idvisitantes)
    {
        $rowset = $this->tableGateway->update($data, ["idvisitantes" => $idvisitantes]);
    }
    public function agregarVisitantes($data)
    {
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }
    public function eliminarVisitantes($idvisitantes)
    {
        $this->tableGateway->delete(["idvisitantes" => $idvisitantes]);
    }
    public function obtenerFiltroDatosVisitantes($start, $length, $search = null, $idferias = null, $idperfil = null, $totalregistro = false)
    {
        $condiciones = [];
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT v.*, f.nombre AS feria
        FROM visitantes v
        LEFT JOIN ferias f ON f.idferias = v.idferias";
        if ($idperfil != 1 && $idferias != null) {
            $condiciones[] = "idferias = {$idferias}";
        }
        if ($search != null) {
            $condiciones[] = "v.nombres LIKE '%" . $search . "%' OR v.apellido_paterno LIKE '%" . $search . "%' OR v.apellido_materno LIKE '%" . $search . "%' OR v.numero_documento LIKE '%" . $search . "%' OR v.correo LIKE '%" . $search . "%' OR f.nombre LIKE '%" . $search . "%' OR v.telefono LIKE '%" . $search . "%'";
        }
        if (! empty($condiciones)) {
            $sql .= " WHERE " . implode(" AND ", $condiciones);
        }
        $sql .= " ORDER BY v.nombres ASC";
        if (! $totalregistro) {
            $sql .= " LIMIT {$start},{$length}";
        }
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
    public function totalUsuariosEfectivos($idferias = null, $fecha = null)
    {
        if ($fecha != null) {
            $fechas = explode(" - ", $fecha);
            $fecha_inicio = date('Y-m-d', strtotime(str_replace("/", "-", $fechas[0])));
            $fecha_fin = date('Y-m-d', strtotime(str_replace("/", "-", $fechas[1])));
        }
        $condiciones = [];
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT COUNT(*) AS total FROM visitantes";
        $condiciones[] = "idferias = {$idferias}";
        $condiciones[] = "fecha_registro_web IS NOT NULL";
        if ($fecha != null) {
            $condiciones[] = "fecha_registro_web >= '{$fecha_inicio} 00:00:00' AND fecha_registro_web <= '{$fecha_fin} 23:59:59'";
        }
        if (! empty($condiciones)) {
            $sql .= " WHERE " . implode(" AND ", $condiciones);
        }
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->current();
        return (int)$data['total'];
    }
    public function createFieldVisitantes($fields)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SHOW COLUMNS FROM visitantes";
        $dataFields = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        $listFields = [];
        foreach ($dataFields as $field) {
            $listFields[] = $field['Field'];
        }
        if (! empty($fields)) {
            foreach ($fields as $field) {
                if (! in_array($field, $listFields)) {
                    $sqlCreateField = "ALTER TABLE visitantes ADD {$field} VARCHAR(250) NULL";
                    $adapter->query($sqlCreateField, $adapter::QUERY_MODE_EXECUTE);
                }
            }
        }
        return true;
    }
}
