<?php

namespace Application\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\Sql\Select;

class SpeakersTable
{
    protected $tableGateway;
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    public function obtenerSpeakers($idferias, $idperfil, $idusuario = null, $encargado = null)
    {
        $condiciones = [];
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT e.*, f.nombre AS feria
        FROM speakers e
        LEFT JOIN ferias f ON f.idferias = e.idferias";
        if ($idperfil != 1 && $idferias != null) {
            $condiciones[] = "e.idferias = {$idferias}";
        }
        if ($idperfil != 1 && $idusuario != null && $encargado != 1) {
            $condiciones[] = "e.idusuario = {$idusuario}";
        }
        if (! empty($condiciones)) {
            $sql .= " WHERE " . implode(" AND ", $condiciones);
        }
        $sql .= " ORDER BY e.idspeakers DESC";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
    public function obtenerDatoSpeakers($where)
    {
        $rowset = $this->tableGateway->select($where);
        return $rowset->current();
    }
    public function actualizarDatosSpeakers($data, $idspeakers)
    {
        $rowset = $this->tableGateway->update($data, ["idspeakers" => $idspeakers]);
    }
    public function agregarSpeakers($data)
    {
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }
    public function eliminarSpeakers($idspeakers)
    {
        $this->tableGateway->delete(["idspeakers" => $idspeakers]);
    }
    public function obtenerFiltroDatosSpeakers($start, $length, $search = null, $totalregistro = false)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM speakers";
        $sql .= " ORDER BY idspeakers ASC";
        if (! $totalregistro) {
            $sql .= " LIMIT {$start},{$length}";
        }
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
}
