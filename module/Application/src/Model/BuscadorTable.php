<?php

namespace Application\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\Sql\Select;

class BuscadorTable
{
    protected $tableGateway;
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    public function obtenerBuscador($idferias = null, $idperfil = null, $idusuario = null, $encargado = null)
    {
        $condiciones = [];
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT b.*, e.nombre AS empresa, f.nombre AS feria, f.idplanes, CONCAT(u.nombres, ' ', u.apellido_paterno, ' ', u.apellido_materno) AS usuario
        FROM buscador b
        LEFT JOIN empresas e ON e.idempresas = b.idempresas
        LEFT JOIN ferias f ON f.idferias = b.idferias
        LEFT JOIN fd_usuarios u ON u.idusuario = b.idusuario";
        if ($idperfil != 1 && $idferias != null) {
            $condiciones[] = "b.idferias = {$idferias}";
        }
        if ($idperfil != 1 && $idusuario != null && $encargado != 1) {
            $condiciones[] = "b.idusuario = {$idusuario}";
        }
        if (! empty($condiciones)) {
            $sql .= " WHERE " . implode(" AND ", $condiciones);
        }
        $sql .= " ORDER BY b.idbuscador DESC";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
    public function obtenerDatoBuscador($where)
    {
        $rowset = $this->tableGateway->select($where);
        return $rowset->current();
    }
    public function obtenerDatosBuscador($where)
    {
        $rowset = $this->tableGateway->select($where);
        return $rowset->toArray();
    }
    public function actualizarDatosBuscador($data, $idbuscador)
    {
        $rowset = $this->tableGateway->update($data, ["idbuscador" => $idbuscador]);
    }
    public function agregarBuscador($data)
    {
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }
    public function eliminarBuscador($idbuscador)
    {
        $this->tableGateway->delete(["idbuscador" => $idbuscador]);
    }
    public function obtenerFiltroDatosBuscador($start, $length, $search = null, $totalregistro = false)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM buscador";
        $sql .= " ORDER BY idbuscador ASC";
        if (! $totalregistro) {
            $sql .= " LIMIT {$start},{$length}";
        }
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
}
