<?php

namespace Application\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\Sql\Select;

class StandTable {
    protected $tableGateway;
    public function __construct(TableGatewayInterface $tableGateway) {
        $this->tableGateway = $tableGateway;
    }
    public function obtenerStand(){
        $select = $this->tableGateway->getSql()->select();
        $select->order('idstand DESC');
        $rowset = $this->tableGateway->selectWith($select);
        return $rowset->toArray();
    }
    public function obtenerDatoStand($where){
        $rowset = $this->tableGateway->select($where);
        return $rowset->current();
    }
    public function actualizarDatosStand($data,$idstand){
        $rowset = $this->tableGateway->update($data,["idstand" => $idstand]);
    }
    public function agregarStand($data){
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }
	public function agregarStandModelo($data){
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }
    public function eliminarStand($idstand){
        $this->tableGateway->delete(["idstand" => $idstand]);
    }
    public function obtenerFiltroDatosStand($start,$length,$search=null,$totalregistro=false){
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM stand";
        $sql .= " ORDER BY idstand ASC";
        if(!$totalregistro)$sql .= " LIMIT {$start},{$length}";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
}