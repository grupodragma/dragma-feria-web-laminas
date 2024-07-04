<?php

namespace Application\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\Sql\Select;

class ExpositoresProductosTable {
    protected $tableGateway;
    public function __construct(TableGatewayInterface $tableGateway) {
        $this->tableGateway = $tableGateway;
    }
    public function obtenerExpositoresProductos($idexpositores=null){
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM expositores_productos WHERE idexpositores = {$idexpositores}";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
    public function obtenerDatoExpositoresProductos($where){
        $rowset = $this->tableGateway->select($where);
        return $rowset->current();
    }
    public function obtenerDatosExpositoresProductos($where){
        $rowset = $this->tableGateway->select($where);
        return $rowset->toArray();
    }
    public function actualizarDatosExpositoresProductos($data,$idexpositoresproductos){
        $rowset = $this->tableGateway->update($data,["idexpositoresproductos" => $idexpositoresproductos]);
    }
    public function agregarExpositoresProductos($data){
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }
    public function eliminarExpositoresProductos($idexpositoresproductos){
        $this->tableGateway->delete(["idexpositoresproductos" => $idexpositoresproductos]);
    }
    public function obtenerFiltroDatosExpositoresProductos($start,$length,$search=null,$totalregistro=false){
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM expositores_productos";
        $sql .= " ORDER BY idexpositoresproductos ASC";
        if(!$totalregistro)$sql .= " LIMIT {$start},{$length}";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
}