<?php

namespace Application\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\Sql\Select;

class PaginasStandTable {
    protected $tableGateway;
    public function __construct(TableGatewayInterface $tableGateway) {
        $this->tableGateway = $tableGateway;
    }
    public function obtenerPaginasStand(){
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM paginas_stand ORDER BY idpaginasstand DESC";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
    public function obtenerDatoPaginasStand($where){
        $rowset = $this->tableGateway->select($where);
        return $rowset->current();
    }
    public function actualizarDatosPaginasStand($data,$idpaginasstand){
        $rowset = $this->tableGateway->update($data,["idpaginasstand" => $idpaginasstand]);
    }
    public function agregarPaginasStand($data){
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }
    public function eliminarPaginasStand($idpaginasStand){
        $this->tableGateway->delete(["idpaginasstand" => $idpaginasStand]);
    }
}