<?php

namespace Application\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\Sql\Select;

class PaginasTable {
    protected $tableGateway;
    public function __construct(TableGatewayInterface $tableGateway) {
        $this->tableGateway = $tableGateway;
    }
    public function obtenerPaginas(){
        $select = $this->tableGateway->getSql()->select();
        $select->order('idpaginas DESC');
        $rowset = $this->tableGateway->selectWith($select);
        return $rowset->toArray();
    }
    public function obtenerDatoPaginas($where){
        $rowset = $this->tableGateway->select($where);
        return $rowset->current();
    }
    public function actualizarDatosPaginas($data,$idpaginas){
        $rowset = $this->tableGateway->update($data,["idpaginas" => $idpaginas]);
    }
    public function agregarPaginas($data){
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }
    public function eliminarPaginas($idpaginas){
        $this->tableGateway->delete(["idpaginas" => $idpaginas]);
    }
    public function obtenerFiltroDatosPaginas($start,$length,$search=null,$totalregistro=false){
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM paginas";
        $sql .= " ORDER BY idpaginas ASC";
        if(!$totalregistro)$sql .= " LIMIT {$start},{$length}";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
}