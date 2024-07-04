<?php

namespace Application\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\Sql\Select;

class VisitantesRegistrosTable {
    protected $tableGateway;
    public function __construct(TableGatewayInterface $tableGateway) {
        $this->tableGateway = $tableGateway;
    }
    public function obtenerVisitantesRegistros(){
        $select = $this->tableGateway->getSql()->select();
        $select->order('idvisitantesregistros DESC');
        $rowset = $this->tableGateway->selectWith($select);
        return $rowset->toArray();
    }
    public function obtenerDatoVisitantesRegistros($where){
        $rowset = $this->tableGateway->select($where);
        return $rowset->current();
    }
    public function obtenerDatosVisitantesRegistros($where){
        $rowset = $this->tableGateway->select($where);
        return $rowset->toArray();
    }
    public function actualizarDatosVisitantesRegistros($data,$idvisitantesregistros){
        $rowset = $this->tableGateway->update($data,["idvisitantesregistros" => $idvisitantesregistros]);
    }
    public function agregarVisitantesRegistros($data){
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }
    public function eliminarVisitantesRegistros($idvisitantesregistros){
        $this->tableGateway->delete(["idvisitantesregistros" => $idvisitantesregistros]);
    }
    public function obtenerFiltroDatosVisitantesRegistros($start,$length,$search=null,$totalregistro=false){
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM visitantesregistros";
        $sql .= " ORDER BY idvisitantesregistros ASC";
        if(!$totalregistro)$sql .= " LIMIT {$start},{$length}";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
}