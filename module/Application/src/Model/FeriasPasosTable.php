<?php

namespace Application\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\Sql\Select;

class FeriasPasosTable
{
    protected $tableGateway;
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    public function obtenerFeriasPasos()
    {
        $select = $this->tableGateway->getSql()->select();
        $select->order('idferiaspasos DESC');
        $rowset = $this->tableGateway->selectWith($select);
        return $rowset->toArray();
    }
    public function obtenerDatoFeriasPasos($where)
    {
        $rowset = $this->tableGateway->select($where);
        return $rowset->current();
    }
    public function obtenerDatosFeriasPasos($where)
    {
        $rowset = $this->tableGateway->select($where);
        return $rowset->toArray();
    }
    public function actualizarDatosFeriasPasos($data, $idferiaspasos)
    {
        $rowset = $this->tableGateway->update($data, ["idferiaspasos" => $idferiaspasos]);
    }
    public function actualizarDatoFeriasPasos($data, $where)
    {
        $rowset = $this->tableGateway->update($data, $where);
    }
    public function agregarFeriasPasos($data)
    {
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }
    public function eliminarFeriasPasos($idferiaspasos)
    {
        $this->tableGateway->delete(["idferiaspasos" => $idferiaspasos]);
    }
    public function obtenerFiltroDatosFeriasPasos($start, $length, $search = null, $totalregistro = false)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM ferias_pasos";
        $sql .= " ORDER BY idferiaspasos ASC";
        if (! $totalregistro) {
            $sql .= " LIMIT {$start},{$length}";
        }
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
}
