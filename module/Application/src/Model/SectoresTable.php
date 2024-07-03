<?php

namespace Application\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\Sql\Select;

class SectoresTable
{
    protected $tableGateway;
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    public function obtenerSectores()
    {
        $select = $this->tableGateway->getSql()->select();
        $select->order('idsectores DESC');
        $rowset = $this->tableGateway->selectWith($select);
        return $rowset->toArray();
    }
    public function obtenerDatoSectores($where)
    {
        $rowset = $this->tableGateway->select($where);
        return $rowset->current();
    }
    public function obtenerDatosSectores($where)
    {
        $rowset = $this->tableGateway->select($where);
        return $rowset->toArray();
    }
    public function actualizarDatosSectores($data, $idsectores)
    {
        $rowset = $this->tableGateway->update($data, ["idsectores" => $idsectores]);
    }
    public function agregarSectores($data)
    {
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }
    public function eliminarSectores($idsectores)
    {
        $this->tableGateway->delete(["idsectores" => $idsectores]);
    }
    public function obtenerFiltroDatosSectores($start, $length, $search = null, $totalregistro = false)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM sectores";
        $sql .= " ORDER BY idsectores ASC";
        if (! $totalregistro) {
            $sql .= " LIMIT {$start},{$length}";
        }
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
}
