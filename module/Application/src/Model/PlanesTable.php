<?php

namespace Application\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\Sql\Select;

class PlanesTable
{
    protected $tableGateway;
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    public function obtenerPlanes()
    {
        $select = $this->tableGateway->getSql()->select();
        $select->order('orden ASC');
        $rowset = $this->tableGateway->selectWith($select);
        return $rowset->toArray();
    }
    public function obtenerDatoPlanes($where)
    {
        $rowset = $this->tableGateway->select($where);
        return $rowset->current();
    }
    public function actualizarDatosPlanes($data, $idplanes)
    {
        $rowset = $this->tableGateway->update($data, ["idplanes" => $idplanes]);
    }
    public function agregarPlanes($data)
    {
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }
    public function eliminarPlanes($idplanes)
    {
        $this->tableGateway->delete(["idplanes" => $idplanes]);
    }
    public function obtenerFiltroDatosPlanes($start, $length, $search = null, $totalregistro = false)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM planes";
        $sql .= " ORDER BY idplanes ASC";
        if (! $totalregistro) {
            $sql .= " LIMIT {$start},{$length}";
        }
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
    public function obtenerUltimoOrdenPlanes()
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT (MAX(orden) + 1) AS siguiente_orden FROM planes";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->current();
        return $data['siguiente_orden'];
    }
    public function actualizarOrdenPlanes()
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM planes ORDER BY orden ASC";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        if (! empty($data)) {
            $i = 1;
            foreach ($data as $item) {
                $this->actualizarDatosPlanes(['orden' => $i], $item['idplanes']);
                $i++;
            }
        }
    }
}
