<?php

namespace Application\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\Sql\Select;

class FeriasPromocionesTable
{
    protected $tableGateway;
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    public function obtenerFeriasPromociones($idferias)
    {
        $condiciones = [];
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT fp.*, p.nombre AS promocion, p.hash_imagen AS imagen_promocion, e.nombre AS empresa
        FROM ferias_promociones fp
        INNER JOIN promociones p ON p.idpromociones = fp.idpromociones
        INNER JOIN empresas e ON e.idempresas = p.idempresas
        INNER JOIN zonas z ON z.idzonas = e.idzonas";
        if ($idferias != null) {
            $condiciones[] = "fp.idferias = {$idferias}";
        }
        if (! empty($condiciones)) {
            $sql .= " WHERE " . implode(" AND ", $condiciones);
        }
        $sql .= " ORDER BY fp.fecha_inicio, fp.fecha_fin DESC";
        return $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
    }
    public function obtenerDatoFeriasPromociones($where)
    {
        $rowset = $this->tableGateway->select($where);
        return $rowset->current();
    }
    public function obtenerDatosFeriasPromociones($where)
    {
        $rowset = $this->tableGateway->select($where);
        return $rowset->toArray();
    }
    public function actualizarDatoFeriasPromociones($data, $where)
    {
        $this->tableGateway->update($data, $where);
    }
    public function agregarFeriasPromociones($data)
    {
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }
    public function eliminarFeriasPromociones($idferiaspromociones)
    {
        $this->tableGateway->delete(["idferiaspromociones" => $idferiaspromociones]);
    }
    public function existeFechasProgramadas($fechaInicio, $fechaFin, $idferias)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM ferias_promociones WHERE idferias = {$idferias} AND ('{$fechaInicio}' BETWEEN fecha_inicio AND fecha_fin OR '{$fechaFin}' BETWEEN fecha_inicio AND fecha_fin)";
        return $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->current();
    }
}
