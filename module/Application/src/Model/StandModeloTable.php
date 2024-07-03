<?php

namespace Application\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\Sql\Select;

class StandModeloTable
{
    protected $tableGateway;
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    public function obtenerStandModelo()
    {
        $select = $this->tableGateway->getSql()->select();
        $select->order('idstandmodelo DESC');
        $rowset = $this->tableGateway->selectWith($select);
        return $rowset->toArray();
    }
    public function obtenerDatoStandModelo($where)
    {
        $rowset = $this->tableGateway->select($where);
        return $rowset->current();
    }
    public function actualizarDatosStandModelo($data, $idstandmodelo)
    {
        $rowset = $this->tableGateway->update($data, ["idstandmodelo" => $idstandmodelo]);
    }
    public function agregarStandModelo($data)
    {
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }

    public function eliminarStandModelo($idstandmodelo)
    {
        $this->tableGateway->delete(["idstandmodelo" => $idstandmodelo]);
    }
    public function obtenerFiltroDatosStandModelo($start, $length, $search = null, $totalregistro = false)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM standmodelo";
        $sql .= " ORDER BY idstandmodelo ASC";
        if (! $totalregistro) {
            $sql .= " LIMIT {$start},{$length}";
        }
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
}
