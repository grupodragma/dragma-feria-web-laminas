<?php

namespace Application\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\Sql\Select;

class BancosTable {
    protected $tableGateway;
    public function __construct(TableGatewayInterface $tableGateway) {
        $this->tableGateway = $tableGateway;
    }
    public function obtenerBancos(){
        $select = $this->tableGateway->getSql()->select();
        $select->order('idbancos DESC');
        $rowset = $this->tableGateway->selectWith($select);
        return $rowset->toArray();
    }
    public function obtenerDatoBancos($where){
        $rowset = $this->tableGateway->select($where);
        return $rowset->current();
    }
    public function obtenerDatosBancos($where){
        $rowset = $this->tableGateway->select($where);
        return $rowset->toArray();
    }
    public function actualizarDatosBancos($data,$idbancos){
        $rowset = $this->tableGateway->update($data,["idbancos" => $idbancos]);
    }
    public function agregarBancos($data){
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }
    public function eliminarBancos($idbancos){
        $this->tableGateway->delete(["idbancos" => $idbancos]);
    }
    public function obtenerFiltroDatosBancos($start,$length,$search=null,$totalregistro=false){
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM bancos";
        $sql .= " ORDER BY idbancos ASC";
        if(!$totalregistro)$sql .= " LIMIT {$start},{$length}";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
}