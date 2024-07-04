<?php

namespace Application\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\Sql\Select;

class PaginasFeriasTable {
    protected $tableGateway;
    public function __construct(TableGatewayInterface $tableGateway) {
        $this->tableGateway = $tableGateway;
    }
    public function obtenerPaginasFerias(){
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT pf.*, p.nombre AS pagina, f.nombre AS feria FROM paginas_ferias pf
        LEFT JOIN paginas p ON p.idpaginas = pf.idpaginas
        LEFT JOIN ferias f ON f.idferias = pf.idferias
        ORDER BY pf.idpaginasferias DESC";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
    public function obtenerDatoPaginasFerias($where){
        $rowset = $this->tableGateway->select($where);
        return $rowset->current();
    }
    public function actualizarDatosPaginasFerias($data,$idpaginasferias){
        $rowset = $this->tableGateway->update($data,["idpaginasferias" => $idpaginasferias]);
    }
    public function agregarPaginasFerias($data){
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }
    public function eliminarPaginasFerias($idpaginasferias){
        $this->tableGateway->delete(["idpaginasferias" => $idpaginasferias]);
    }
    public function obtenerFiltroDatosPaginasFerias($start,$length,$search=null,$totalregistro=false){
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM paginas_ferias";
        $sql .= " ORDER BY idpaginasferias ASC";
        if(!$totalregistro)$sql .= " LIMIT {$start},{$length}";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
}