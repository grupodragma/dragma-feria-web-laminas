<?php

namespace Application\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\Sql\Select;

class StandGaleriaTable {
    protected $tableGateway;
    public function __construct(TableGatewayInterface $tableGateway) {
        $this->tableGateway = $tableGateway;
    }
    public function obtenerStandGaleria(){
        $select = $this->tableGateway->getSql()->select();
        $select->order('idstandgaleria DESC');
        $rowset = $this->tableGateway->selectWith($select);
        return $rowset->toArray();
    }
    public function obtenerDatoStandGaleria($where){
        $rowset = $this->tableGateway->select($where);
        return $rowset->current();
    }
    public function obtenerDatosStandGaleria($idstand = null){
        $condiciones = [];
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM stand_galeria s";
        if( $idstand != null ) $condiciones[] = "s.idstand = {$idstand}";
        if( !empty($condiciones) ) $sql .= " WHERE ".implode(" AND ", $condiciones);
        $sql .= " ORDER BY idstandgaleria DESC";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
    public function actualizarDatosStandGaleria($data,$idstandgaleria){
        $rowset = $this->tableGateway->update($data,["idstandgaleria" => $idstandgaleria]);
    }
    public function agregarStandGaleria($data){
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }
    public function eliminarStandGaleria($idstandgaleria){
        $this->tableGateway->delete(["idstandgaleria" => $idstandgaleria]);
    }
    public function obtenerFiltroDatosStandGaleria($start,$length,$search=null,$totalregistro=false){
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM stand_galeria";
        $sql .= " ORDER BY idstandgaleria ASC";
        if(!$totalregistro)$sql .= " LIMIT {$start},{$length}";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
    public function imagenPrimarioStandGaleria($idstandgaleria){
        $data = $this->tableGateway->select()->toArray();
        if(!empty($data)){
            foreach($data as $item){
                $primario = ( $item['idstandgaleria'] == $idstandgaleria ) ? 1 : 0;
                $this->actualizarDatosStandGaleria(['primario'=> $primario], $item['idstandgaleria']);
            }
        }
    }
}