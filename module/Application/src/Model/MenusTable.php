<?php

namespace Application\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\Sql\Select;

class MenusTable
{
    protected $tableGateway;
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    public function obtenerMenus()
    {
        $select = $this->tableGateway->getSql()->select();
        $select->order('idmenus DESC');
        $rowset = $this->tableGateway->selectWith($select);
        return $rowset->toArray();
    }
    public function obtenerDatoMenus($where)
    {
        $rowset = $this->tableGateway->select($where);
        return $rowset->current();
    }
    public function obtenerDatosMenus($where)
    {
        $rowset = $this->tableGateway->select($where);
        return $rowset->toArray();
    }
    public function actualizarDatosMenus($data, $idmenus)
    {
        $rowset = $this->tableGateway->update($data, ["idmenus" => $idmenus]);
    }
    public function agregarMenus($data)
    {
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }
    public function eliminarMenus($idmenus)
    {
        $this->tableGateway->delete(["idmenus" => $idmenus]);
    }
    public function obtenerFiltroDatosMenus($start, $length, $search = null, $totalregistro = false)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM menus";
        $sql .= " ORDER BY idmenus ASC";
        if (! $totalregistro) {
            $sql .= " LIMIT {$start},{$length}";
        }
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
}
