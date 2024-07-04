<?php

namespace Application\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\Sql\Select;

class PaginasBotonesTable {
    protected $tableGateway;
    protected $adapter;
    public function __construct(TableGatewayInterface $tableGateway, $adapter) {
        $this->tableGateway = $tableGateway;
        $this->adapter = $adapter;
    }
    public function obtenerDatoPaginasBotones($where){
        $rowset = $this->tableGateway->select($where);
        return $rowset->current();
    }
    public function obtenerDatosPaginasBotones($where){
        $rowset = $this->tableGateway->select($where);
        return $rowset->toArray();
    }
    public function actualizarDatosPaginasBotones($data,$where){
        return $this->tableGateway->update($data,$where);
    }
    public function agregarPaginasBotones($data){
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }
    public function eliminarPaginasBotones($idpaginasbotones){
        $this->tableGateway->delete(["idpaginasbotones" => $idpaginasbotones]);
    }
}