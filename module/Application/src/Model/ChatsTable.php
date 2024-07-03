<?php

namespace Application\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\Sql\Select;

class ChatsTable
{
    protected $tableGateway;
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    public function obtenerChats()
    {
        $select = $this->tableGateway->getSql()->select();
        $select->order('idchats DESC');
        $rowset = $this->tableGateway->selectWith($select);
        return $rowset->toArray();
    }
    public function obtenerDatoChats($where)
    {
        $rowset = $this->tableGateway->select($where);
        return $rowset->current();
    }
    public function obtenerDatosChats($where)
    {
        $rowset = $this->tableGateway->select($where);
        return $rowset->toArray();
    }
    public function actualizarDatosChats($data, $idchats)
    {
        $rowset = $this->tableGateway->update($data, ["idchats" => $idchats]);
    }
    public function agregarChats($data)
    {
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }
    public function eliminarChats($idchats)
    {
        $this->tableGateway->delete(["idchats" => $idchats]);
    }
    public function obtenerFiltroDatosChats($start, $length, $search = null, $totalregistro = false)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM chats";
        $sql .= " ORDER BY idchats ASC";
        if (! $totalregistro) {
            $sql .= " LIMIT {$start},{$length}";
        }
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
    public function obtenerHistorialChats($codigoUnico)
    {
        $condiciones = [];
        $adapter = $this->tableGateway->getAdapter();
        $sql = 'SELECT *,
            (
                CASE
                    WHEN c.tipo_usuario = "S" THEN (SELECT CONCAT(nombres," ", apellido_paterno," ", apellido_materno) FROM speakers WHERE idspeakers = c.idusuario)
                    WHEN c.tipo_usuario = "E" THEN (SELECT CONCAT(nombres," ", apellido_paterno," ", apellido_materno) FROM expositores WHERE idexpositores = c.idusuario)
                    WHEN c.tipo_usuario = "V" THEN (SELECT CONCAT(nombres," ", apellido_paterno," ", apellido_materno) FROM visitantes WHERE idvisitantes = c.idusuario)
                    ELSE 0
                END
            ) AS usuario
        FROM chats c';
        $condiciones[] = "c.codigoUnico = {$codigoUnico}";
        if (! empty($condiciones)) {
            $sql .= " WHERE " . implode(" AND ", $condiciones);
        }
        $sql .= " ORDER BY c.idchats ASC";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
}
