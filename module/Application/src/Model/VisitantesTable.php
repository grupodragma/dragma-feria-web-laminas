<?php

namespace Application\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\Sql\Select;

class VisitantesTable {
    protected $tableGateway;
    public function __construct(TableGatewayInterface $tableGateway) {
        $this->tableGateway = $tableGateway;
    }
    public function obtenerVisitantes(){
        $select = $this->tableGateway->getSql()->select();
        $select->order('idvisitantes DESC');
        $rowset = $this->tableGateway->selectWith($select);
        return $rowset->toArray();
    }
    public function obtenerDatoVisitantes($where){
        $rowset = $this->tableGateway->select($where);
        return $rowset->current();
    }
    public function actualizarDatosVisitantes($data,$idvisitantes){
        $rowset = $this->tableGateway->update($data,["idvisitantes" => $idvisitantes]);
    }
    public function agregarVisitantes($data){
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }
    public function eliminarVisitantes($idvisitantes){
        $this->tableGateway->delete(["idvisitantes" => $idvisitantes]);
    }
    public function obtenerFiltroDatosVisitantes($start,$length,$search=null,$idusuario=null,$idperfil=null,$totalregistro=false){
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM visitantes";
        $sql .= " WHERE idvisitantes IS NOT NULL";
        if($idperfil != 1 && $idusuario != null)$sql .= " AND idusuario = {$idusuario}";
        if($search!=null)$sql .= " AND nombres LIKE '%".$search."%' OR apellido_paterno LIKE '%".$search."%' OR apellido_materno LIKE '%".$search."%' OR numero_documento LIKE '%".$search."%' OR correo LIKE '%".$search."%'";
        $sql .= " ORDER BY nombres ASC";
        if(!$totalregistro)$sql .= " LIMIT {$start},{$length}";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
}