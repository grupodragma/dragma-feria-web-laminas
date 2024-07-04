<?php

namespace Application\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\Sql\Select;

class OfertasTable {
    protected $tableGateway;
    public function __construct(TableGatewayInterface $tableGateway) {
        $this->tableGateway = $tableGateway;
    }
    public function obtenerDatosOfertas($idferias){
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT o.*, e.nombre AS empresa, e.hash_logo AS hash_logo_empresa
        FROM ofertas o
        LEFT JOIN empresas e ON e.idempresas = o.idempresas
		LEFT JOIN zonas z ON z.idzonas = e.idzonas
        WHERE z.idferias = {$idferias}
        ORDER BY o.idofertas DESC";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
    public function obtenerDatoOfertas($where){
        $rowset = $this->tableGateway->select($where);
        return $rowset->current();
    }
    public function actualizarDatosOfertas($data,$idofertas){
        $rowset = $this->tableGateway->update($data,["idofertas" => $idofertas]);
    }
    public function agregarOfertas($data){
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }
    public function eliminarOfertas($idofertas){
        $this->tableGateway->delete(["idofertas" => $idofertas]);
    }
    public function obtenerFiltroDatosOfertas($start,$length,$search=null,$totalregistro=false){
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM ofertas";
        $sql .= " ORDER BY idofertas ASC";
        if(!$totalregistro)$sql .= " LIMIT {$start},{$length}";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
}