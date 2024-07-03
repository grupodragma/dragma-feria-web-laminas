<?php

namespace Application\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\Sql\Select;

class CronogramasTable
{
    protected $tableGateway;
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    public function obtenerCronogramas($idferias, $idperfil, $idusuario = null, $encargado = null)
    {
        $condiciones = [];
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT c.*, f.nombre as feria, CONCAT(IFNULL(e.nombres, ''),' ', IFNULL(e.apellido_paterno, ''),' ', IFNULL(e.apellido_materno, '')) AS expositor
        FROM cronogramas c
        LEFT JOIN ferias f ON f.idferias = c.idferias
        LEFT JOIN expositores e ON e.idexpositores = c.idexpositores";
        if ($idperfil != 1 && $idferias != null) {
            $condiciones[] = "f.idferias = {$idferias}";
        }
        if ($idperfil != 1 && $idusuario != null && $encargado != 1) {
            $condiciones[] = "c.idusuario = {$idusuario}";
        }
        if (! empty($condiciones)) {
            $sql .= " WHERE " . implode(" AND ", $condiciones);
        }
        $sql .= " ORDER BY c.idcronogramas DESC";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
    public function obtenerDatoCronogramas($where)
    {
        $rowset = $this->tableGateway->select($where);
        return $rowset->current();
    }
    public function actualizarDatosCronogramas($data, $idcronogramas)
    {
        $rowset = $this->tableGateway->update($data, ["idcronogramas" => $idcronogramas]);
    }
    public function agregarCronogramas($data)
    {
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }
    public function eliminarCronogramas($idcronogramas)
    {
        $this->tableGateway->delete(["idcronogramas" => $idcronogramas]);
    }
    public function obtenerFiltroDatosCronogramas($start, $length, $search = null, $totalregistro = false)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM cronogramas";
        $sql .= " ORDER BY idcronogramas ASC";
        if (! $totalregistro) {
            $sql .= " LIMIT {$start},{$length}";
        }
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
}
