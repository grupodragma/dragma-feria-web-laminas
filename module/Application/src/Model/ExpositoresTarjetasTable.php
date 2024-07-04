<?php

namespace Application\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\Sql\Select;

class ExpositoresTarjetasTable {
    protected $tableGateway;
    public function __construct(TableGatewayInterface $tableGateway) {
        $this->tableGateway = $tableGateway;
    }
    public function obtenerExpositoresTarjetas(){
        $select = $this->tableGateway->getSql()->select();
        $select->order('idexpositorestarjetas DESC');
        $rowset = $this->tableGateway->selectWith($select);
        return $rowset->toArray();
    }
    public function obtenerDatoExpositoresTarjetas($where){
        $rowset = $this->tableGateway->select($where);
        return $rowset->current();
    }
    public function obtenerDatosExpositoresTarjetas($where){
        $rowset = $this->tableGateway->select($where);
        return $rowset->toArray();
    }
    public function actualizarDatosExpositoresTarjetas($data,$idexpositorestarjetas){
        $rowset = $this->tableGateway->update($data,["idexpositorestarjetas" => $idexpositorestarjetas]);
    }
    public function agregarExpositoresTarjetas($data){
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }
    public function eliminarExpositoresTarjetas($idexpositorestarjetas){
        $this->tableGateway->delete(["idexpositorestarjetas" => $idexpositorestarjetas]);
    }
    public function obtenerFiltroDatosExpositoresTarjetas($start,$length,$search=null,$totalregistro=false){
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM expositorestarjetas";
        $sql .= " ORDER BY idexpositorestarjetas ASC";
        if(!$totalregistro)$sql .= " LIMIT {$start},{$length}";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
}