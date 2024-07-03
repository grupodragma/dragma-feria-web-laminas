<?php

namespace Application\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\Sql\Select;

class PlanesPaginasTable
{
    protected $tableGateway;
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    public function obtenerPlanesPaginas()
    {
        $select = $this->tableGateway->getSql()->select();
        $select->order('idplanespaginas DESC');
        $rowset = $this->tableGateway->selectWith($select);
        return $rowset->toArray();
    }
    public function obtenerDatoPlanesPaginas($where)
    {
        $rowset = $this->tableGateway->select($where);
        return $rowset->current();
    }
    public function obtenerDatosPlanesPaginas($where)
    {
        $rowset = $this->tableGateway->select($where);
        return $rowset->toArray();
    }
    public function obtenerPaginasPorIdPlanes($idplanes)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT pp.*, p.nombre as pagina FROM planes_paginas pp
        INNER JOIN paginas p ON p.idpaginas = pp.idpaginas
        WHERE pp.idplanes = {$idplanes}";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
    public function actualizarDatosPlanesPaginas($data, $idplanespaginas)
    {
        $rowset = $this->tableGateway->update($data, ["idplanespaginas" => $idplanespaginas]);
    }
    public function agregarPlanesPaginas($data)
    {
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }
    public function eliminarPlanesPaginas($where)
    {
        $this->tableGateway->delete($where);
    }
    public function obtenerFiltroDatosPlanesPaginas($start, $length, $search = null, $totalregistro = false)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM planespaginas";
        $sql .= " ORDER BY idplanespaginas ASC";
        if (! $totalregistro) {
            $sql .= " LIMIT {$start},{$length}";
        }
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
}
