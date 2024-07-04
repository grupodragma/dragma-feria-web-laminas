<?php

namespace Application\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\Sql\Select;

class SeoTable {
    protected $tableGateway;
    public function __construct(TableGatewayInterface $tableGateway) {
        $this->tableGateway = $tableGateway;
    }
    public function obtenerSeo(){
        $select = $this->tableGateway->getSql()->select();
        $select->order('idseo DESC');
        $rowset = $this->tableGateway->selectWith($select);
        return $rowset->toArray();
    }
    public function obtenerDatoSeo($where){
        $rowset = $this->tableGateway->select($where);
        return $rowset->current();
    }
    public function obtenerSeoFeria($idferias, $idmenus){
        $rowset = $this->tableGateway->select(['idferias'=> $idferias, 'idmenus'=> $idmenus]);
        return $rowset->current();
    }
    public function obtenerDatosSeo($where){
        $rowset = $this->tableGateway->select($where);
        return $rowset->toArray();
    }
    public function actualizarDatosSeo($data,$idseo){
        $rowset = $this->tableGateway->update($data,["idseo" => $idseo]);
    }
    public function agregarSeo($data){
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }
    public function eliminarSeo($idseo){
        $this->tableGateway->delete(["idseo" => $idseo]);
    }
    public function obtenerFiltroDatosSeo($start,$length,$search=null,$totalregistro=false){
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM seo";
        $sql .= " ORDER BY idseo ASC";
        if(!$totalregistro)$sql .= " LIMIT {$start},{$length}";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
}