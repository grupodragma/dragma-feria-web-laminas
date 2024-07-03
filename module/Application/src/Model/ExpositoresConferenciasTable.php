<?php

namespace Application\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\Sql\Select;

class ExpositoresConferenciasTable
{
    protected $tableGateway;
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    public function obtenerExpositoresConferencias()
    {
        $select = $this->tableGateway->getSql()->select();
        $select->order('idexpositoresconferencias DESC');
        $rowset = $this->tableGateway->selectWith($select);
        return $rowset->toArray();
    }
    public function obtenerDatoExpositoresConferencias($where)
    {
        $rowset = $this->tableGateway->select($where);
        return $rowset->current();
    }
    public function obtenerDatosExpositoresConferencias($where)
    {
        $rowset = $this->tableGateway->select($where);
        return $rowset->toArray();
    }
    public function actualizarDatosExpositoresConferencias($data, $idexpositoresconferencias)
    {
        $rowset = $this->tableGateway->update($data, ["idexpositoresconferencias" => $idexpositoresconferencias]);
    }
    public function agregarExpositoresConferencias($data)
    {
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }
    public function eliminarExpositoresConferencias($where)
    {
        $this->tableGateway->delete($where);
    }
}
