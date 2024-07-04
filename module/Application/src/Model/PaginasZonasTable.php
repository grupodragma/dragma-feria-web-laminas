<?php

namespace Application\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\Sql\Select;

class PaginasZonasTable {
    protected $tableGateway;
    public function __construct(TableGatewayInterface $tableGateway) {
        $this->tableGateway = $tableGateway;
    }
    public function obtenerPaginasZonas(){
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT p.*, p.nombre AS zona, FROM paginas_zonas p
        LEFT JOIN zonas p ON p.idzonas = p.idzonas
        
        ORDER BY p.idpaginaszonas DESC";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
    public function obtenerDatoPaginasZonas($where){
        $rowset = $this->tableGateway->select($where);
        return $rowset->current();
    }
    public function actualizarDatosPaginasZonas($data,$idpaginaszonas){
        $rowset = $this->tableGateway->update($data,["idpaginaszonas" => $idpaginaszonas]);
    }
    public function agregarPaginasZonas($data){
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }
    public function eliminarPaginasZonas($idpaginasZonas){
        $this->tableGateway->delete(["idpaginaszonas" => $idpaginasZonas]);
    }
    public function obtenerFiltroDatosPaginasZonas($start,$length,$search=null,$totalregistro=false){
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM paginas_zonas";
        $sql .= " ORDER BY idpaginaszonas ASC";
        if(!$totalregistro)$sql .= " LIMIT {$start},{$length}";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
}