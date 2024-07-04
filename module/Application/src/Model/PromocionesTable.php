<?php

namespace Application\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\Sql\Select;

class PromocionesTable {
    protected $tableGateway;
    public function __construct(TableGatewayInterface $tableGateway) {
        $this->tableGateway = $tableGateway;
    }
    public function obtenerPromociones($idferias, $idperfil){
        $condiciones = [];
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT p.*, e.nombre AS empresa
        FROM promociones p
        LEFT JOIN empresas e ON e.idempresas = p.idempresas
        LEFT JOIN zonas z ON z.idzonas = e.idzonas";
        if( $idperfil != 1 && $idferias != null ) $condiciones[] = "z.idferias = {$idferias}";
        if( !empty($condiciones) ) $sql .= " WHERE ".implode(" AND ", $condiciones);
        $sql .= " ORDER BY p.idpromociones DESC";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
    public function obtenerDatoPromociones($where){
        $rowset = $this->tableGateway->select($where);
        return $rowset->current();
    }
    public function obtenerDatosPromociones($where){
        $rowset = $this->tableGateway->select($where);
        return $rowset->toArray();
    }
    public function actualizarDatosPromociones($data,$idpromociones){
        $rowset = $this->tableGateway->update($data,["idpromociones" => $idpromociones]);
    }
    public function agregarPromociones($data){
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }
    public function eliminarPromociones($idpromociones){
        $this->tableGateway->delete(["idpromociones" => $idpromociones]);
    }
    public function obtenerFiltroDatosPromociones($start,$length,$search=null,$totalregistro=false){
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM promociones";
        $sql .= " ORDER BY idpromociones ASC";
        if(!$totalregistro)$sql .= " LIMIT {$start},{$length}";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
    public function obtenerPromocionesBusquedaPorFeria($idferias=null,$busqueda=null){
        $condiciones = [];
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT p.*, e.nombre AS empresa, e.orden AS empresa_orden, e.hash_url, z.idzonas, z.nombre AS zona, z.orden AS zona_orden, f.nombre AS feria, f.idplanes
        FROM promociones p
        INNER JOIN empresas e ON e.idempresas = p.idempresas
        INNER JOIN zonas z ON z.idzonas = e.idzonas
        INNER JOIN ferias f ON f.idferias = z.idferias";
        if( $idferias != null ) $condiciones[] = "z.idferias = {$idferias}";
        if( $busqueda != null ) $condiciones[] = "p.nombre LIKE '%{$busqueda}%'";
        $condiciones[] = "p.buscador = 1";
        if( !empty($condiciones) ) $sql .= " WHERE ".implode(" AND ", $condiciones);
        $sql .= " ORDER BY p.nombre DESC";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
}