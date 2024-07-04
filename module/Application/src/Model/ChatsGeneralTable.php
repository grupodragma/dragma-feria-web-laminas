<?php

namespace Application\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\Sql\Select;

class ChatsGeneralTable {
    
    protected $tableGateway;
    protected $tableGatewayExpositores;

    public function __construct(TableGatewayInterface $tableGateway, $tableGatewayExpositores) {
        $this->tableGateway = $tableGateway;
        $this->tableGatewayExpositores = $tableGatewayExpositores;
    }
    public function obtenerChatsGeneral(){
        $select = $this->tableGateway->getSql()->select();
        $select->order('idchatsgeneral DESC');
        $rowset = $this->tableGateway->selectWith($select);
        return $rowset->toArray();
    }
    public function obtenerDatoChatsGeneral($where){
        $rowset = $this->tableGateway->select($where);
        return $rowset->current();
    }
    public function obtenerDatosChatsGeneral($where){
        $rowset = $this->tableGateway->select($where);
        return $rowset->toArray();
    }
    public function actualizarDatosChatsGeneral($data,$idchatsgeneral){
        $rowset = $this->tableGateway->update($data,["idchatsgeneral" => $idchatsgeneral]);
    }
    public function agregarChatsGeneral($data){
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }
    public function eliminarChatsGeneral($idchatsgeneral){
        $this->tableGateway->delete(["idchatsgeneral" => $idchatsgeneral]);
    }
    public function obtenerFiltroDatosChatsGeneral($start,$length,$search=null,$totalregistro=false){
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM chats";
        $sql .= " ORDER BY idchatsgeneral ASC";
        if(!$totalregistro)$sql .= " LIMIT {$start},{$length}";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
    public function obtenerHistorialChatsConferencias($idferias, $idcronogramas){
        if($idcronogramas == null)$idcronogramas = 0;
        $condiciones = [];
        $adapter = $this->tableGateway->getAdapter();
        $sql = 'SELECT *,
        (
            CASE
            WHEN cc.tipo_usuario = "S" THEN (SELECT CONCAT(nombres," ", apellido_paterno," ", apellido_materno) FROM speakers WHERE idspeakers = cc.idusuario)
            WHEN cc.tipo_usuario = "E" THEN (SELECT CONCAT(nombres," ", apellido_paterno," ", apellido_materno) FROM expositores WHERE idexpositores = cc.idusuario)
            WHEN cc.tipo_usuario = "V" THEN (SELECT CONCAT(nombres," ", apellido_paterno," ", apellido_materno) FROM visitantes WHERE idvisitantes = cc.idusuario)
            ELSE 0
            END
        ) AS usuario
        FROM chats_conferencias cc';
        $condiciones[] = "cc.idcronogramas = {$idcronogramas}";
        if( !empty($condiciones) ) $sql .= " WHERE ".implode(" AND ", $condiciones);
        $sql .= " ORDER BY fecha_hora ASC";
        
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        $response = [];
        if(!empty($data)){
            foreach($data as $item){
                if($item['tipo_usuario'] === 'E'){
                    $item['empresa'] = $this->tableGatewayExpositores->obtenerExpositorEmpresa($idferias, $item['idusuario']);
                }
                $response[] = $item;
            }
        }
        return $response;
    }
}