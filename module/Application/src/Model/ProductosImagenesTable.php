<?php

namespace Application\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\Sql\Select;

class ProductosImagenesTable {
    protected $tableGateway;
    public function __construct(TableGatewayInterface $tableGateway) {
        $this->tableGateway = $tableGateway;
    }
    public function obtenerProductosImagenes(){
        $select = $this->tableGateway->getSql()->select();
        $select->order('idproductosimagenes DESC');
        $rowset = $this->tableGateway->selectWith($select);
        return $rowset->toArray();
    }
    public function obtenerDatoProductosImagenes($where){
        $rowset = $this->tableGateway->select($where);
        return $rowset->current();
    }
    public function obtenerDatosProductosImagenes($where){
        $rowset = $this->tableGateway->select($where);
        return $rowset->toArray();
    }
    public function actualizarDatosProductosImagenes($data,$idproductosimagenes){
        $rowset = $this->tableGateway->update($data,["idproductosimagenes" => $idproductosimagenes]);
    }
    public function agregarProductosImagenes($data){
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }
    public function eliminarProductosImagenes($idproductosimagenes){
        $this->tableGateway->delete(["idproductosimagenes" => $idproductosimagenes]);
    }
    public function obtenerFiltroDatosProductosImagenes($start,$length,$search=null,$totalregistro=false){
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM productosimagenes";
        $sql .= " ORDER BY idproductosimagenes ASC";
        if(!$totalregistro)$sql .= " LIMIT {$start},{$length}";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
    public function eliminarImagenesOcultos($idsproductosimagenes=null){
        //print_r($idsproductosimagenes);
        if(empty($idsproductosimagenes))return;
        $directorioImagen = getcwd().'/public/productos/slider';
        foreach($idsproductosimagenes as $idproductosimagenes) {
            $dataProductosImagenes = $this->obtenerDatoProductosImagenes(['idproductosimagenes'=> $idproductosimagenes]);
            if( $dataProductosImagenes ) {
                if(file_exists($directorioImagen.'/'.$dataProductosImagenes['hash_imagen'])){
                    @unlink($directorioImagen.'/'.$dataProductosImagenes['hash_imagen']);
                }
            }
            $this->eliminarProductosImagenes($idproductosimagenes);
        }
    }
}