<?php

namespace Application\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\Sql\Select;

class FeriasCorreosTable
{
    protected $tableGateway;
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    public function obtenerFeriasCorreos()
    {
        $select = $this->tableGateway->getSql()->select();
        $select->order('idferiascorreos DESC');
        $rowset = $this->tableGateway->selectWith($select);
        return $rowset->toArray();
    }
    public function obtenerDatoFeriasCorreos($where)
    {
        $rowset = $this->tableGateway->select($where);
        return $rowset->current();
    }
    public function obtenerDatosFeriasCorreos($where)
    {
        $rowset = $this->tableGateway->select($where);
        return $rowset->toArray();
    }
    public function actualizarDatoFeriasCorreos($data, $where)
    {
        $this->tableGateway->update($data, $where);
    }
    public function agregarFeriasCorreos($data)
    {
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }
    public function eliminarFeriasCorreos($idferiascorreos)
    {
        $this->tableGateway->delete(["idferiascorreos" => $idferiascorreos]);
    }
}
