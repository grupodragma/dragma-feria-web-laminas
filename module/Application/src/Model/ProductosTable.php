<?php

namespace Application\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\Sql\Select;

class ProductosTable {
    protected $tableGateway;
    protected $tableGatewayProductosImagenes;
    public function __construct(TableGatewayInterface $tableGateway, $tableGatewayProductosImagenes) {
        $this->tableGateway = $tableGateway;
        $this->tableGatewayProductosImagenes = $tableGatewayProductosImagenes;
    }
    public function obtenerProductos(){
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT p.*, e.nombre AS empresa FROM productos p
        LEFT JOIN empresas e ON e.idempresas = p.idempresas
        ORDER BY p.idproductos DESC";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
    public function buscarProductosPorFeria($idferias=null, $busqueda=null){
        $condiciones = [];
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT p.*, e.nombre AS empresa, e.orden AS empresa_orden, z.idzonas, z.nombre AS zona, z.orden AS zona_orden 
        FROM productos p
        LEFT JOIN empresas e ON e.idempresas = p.idempresas
        LEFT JOIN zonas z ON z.idzonas = e.idzonas";
        if( $idferias != null ) $condiciones[] = "z.idferias = {$idferias}";
        if( $busqueda != null ) $condiciones[] = "p.descripcion LIKE '%{$busqueda}%'";
        $condiciones[] = "p.buscador = 1";
        if( !empty($condiciones) ) $sql .= " WHERE ".implode(" AND ", $condiciones);
        $sql .= " ORDER BY p.nombre ASC";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
    public function obtenerProductosPorFeria($idferias=null){
        $condiciones = [];
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT p.*, e.nombre AS empresa, e.orden AS empresa_orden, z.idzonas, z.nombre AS zona, z.orden AS zona_orden 
        FROM productos p
        LEFT JOIN empresas e ON e.idempresas = p.idempresas
        LEFT JOIN zonas z ON z.idzonas = e.idzonas";
        if( $idferias != null ) $condiciones[] = "z.idferias = {$idferias}";
        $condiciones[] = "p.buscador = 1";
        if( !empty($condiciones) ) $sql .= " WHERE ".implode(" AND ", $condiciones);
        $sql .= " ORDER BY p.nombre ASC";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
    public function obtenerDatoProductos($where){
        $rowset = $this->tableGateway->select($where);
        return $rowset->current();
    }
    public function obtenerDatosProductos($where){
        $rowset = $this->tableGateway->select($where);
        return $rowset->toArray();
    }
    public function obtenerDatosProductosImagenes($where){
        $rowset = $this->tableGateway->select($where);
        $data = $rowset->toArray();
        $response = [];
        if(!empty($data)){
            foreach($data as $item){
                $item['imagenes'] = $this->tableGatewayProductosImagenes->obtenerDatosProductosImagenes(['idproductos'=> $item['idproductos']]);
                $response[] = $item;
            }
        }
        return $response;
    }
    public function actualizarDatosProductos($data,$idproductos){
        $rowset = $this->tableGateway->update($data,["idproductos" => $idproductos]);
    }
    public function agregarProductos($data){
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }
    public function eliminarProductos($idproductos){
        $this->tableGateway->delete(["idproductos" => $idproductos]);
    }
    public function obtenerFiltroDatosProductos($start,$length,$search=null,$totalregistro=false){
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM productos";
        $sql .= " ORDER BY idproductos ASC";
        if(!$totalregistro)$sql .= " LIMIT {$start},{$length}";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
}