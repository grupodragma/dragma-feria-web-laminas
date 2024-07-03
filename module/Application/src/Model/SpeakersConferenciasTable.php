<?php

namespace Application\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\Sql\Select;

class SpeakersConferenciasTable
{
    protected $tableGateway;
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    public function obtenerSpeakersConferencias()
    {
        $select = $this->tableGateway->getSql()->select();
        $select->order('idspeakersconferencias DESC');
        $rowset = $this->tableGateway->selectWith($select);
        return $rowset->toArray();
    }
    public function obtenerDatoSpeakersConferencias($where)
    {
        $rowset = $this->tableGateway->select($where);
        return $rowset->current();
    }
    public function obtenerDatosSpeakersConferencias($where)
    {
        $rowset = $this->tableGateway->select($where);
        return $rowset->toArray();
    }
    public function actualizarDatosSpeakersConferencias($data, $idspeakersconferencias)
    {
        $rowset = $this->tableGateway->update($data, ["idspeakersconferencias" => $idspeakersconferencias]);
    }
    public function agregarSpeakersConferencias($data)
    {
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }
    public function eliminarSpeakersConferencias($where)
    {
        $this->tableGateway->delete($where);
    }
}
