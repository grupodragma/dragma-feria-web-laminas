<?php

namespace Application\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\Sql\Select;

class ZonasTable {
    protected $tableGateway;
    public function __construct(TableGatewayInterface $tableGateway) {
        $this->tableGateway = $tableGateway;
    }
    public function obtenerZonas(){
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT z.*, f.nombre AS feria FROM zonas z
        LEFT JOIN ferias f ON f.idferias = z.idferias
        ORDER BY z.idzonas DESC";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
    /* public function obtenerDatosZonas(){
        $condiciones = [];
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT s.*, c.nombre AS categoria FROM subcategorias s
        INNER JOIN categorias c ON c.idcategorias = s.idcategorias";
        if( $idcategorias != null ) $condiciones[] = "c.idcategorias = {$idcategorias}";
        if( !empty($condiciones) ) $sql .= " WHERE ".implode(" AND ", $condiciones);
        $sql .= " ORDER BY s.idsubcategorias DESC";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    } */
    public function obtenerDatoZonas($where){
        $rowset = $this->tableGateway->select($where);
        return $rowset->current();
    }
    public function actualizarDatosZonas($data,$idzonas){
        $rowset = $this->tableGateway->update($data,["idzonas" => $idzonas]);
    }
    public function agregarZonas($data){
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }
    public function eliminarZonas($idzonas){
        $this->tableGateway->delete(["idzonas" => $idzonas]);
    }
    public function obtenerFiltroDatosZonas($start,$length,$search=null,$totalregistro=false){
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM zonas";
        $sql .= " ORDER BY idzonas ASC";
        if(!$totalregistro)$sql .= " LIMIT {$start},{$length}";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
    public function obtenerZonasPorFeria($idferias){
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM zonas WHERE idferias = {$idferias}";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }

    public function obtenerZonasPorFeriaOrder($idferias, $field, $order = "ASC"){
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM zonas WHERE idferias = {$idferias} ORDER BY {$field} {$order}";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
}