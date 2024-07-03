<?php

namespace Application\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\Sql\Select;

class SalasTable
{
    protected $tableGateway;
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    public function obtenerSalas()
    {
        $select = $this->tableGateway->getSql()->select();
        $select->order('idsalas DESC');
        $rowset = $this->tableGateway->selectWith($select);
        return $rowset->toArray();
    }
    public function obtenerDatoSalas($where)
    {
        $rowset = $this->tableGateway->select($where);
        return $rowset->current();
    }
    public function obtenerDatosSalas($where)
    {
        $rowset = $this->tableGateway->select($where);
        return $rowset->toArray();
    }
    public function actualizarDatosSalas($data, $idsalas)
    {
        $rowset = $this->tableGateway->update($data, ["idsalas" => $idsalas]);
    }
    public function agregarSalas($data)
    {
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }
    public function eliminarSalas($idsalas)
    {
        $this->tableGateway->delete(["idsalas" => $idsalas]);
    }
    public function obtenerFiltroDatosSalas($start, $length, $search = null, $totalregistro = false)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM salas";
        $sql .= " ORDER BY idsalas ASC";
        if (! $totalregistro) {
            $sql .= " LIMIT {$start},{$length}";
        }
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
}
