<?php

namespace Application\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\Sql\Select;

class EmpresasTable {
    protected $tableGateway;
    public function __construct(TableGatewayInterface $tableGateway) {
        $this->tableGateway = $tableGateway;
    }
    public function obtenerEmpresas(){
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT e.*, z.nombre AS zona, s.nombre AS stand,  CONCAT(ex.nombres,' ', ex.apellido_paterno,' ', ex.apellido_materno) AS expositor
        FROM empresas e
        LEFT JOIN zonas z ON z.idzonas = e.idzonas
        LEFT JOIN stand s ON s.idstand = e.idstand
        LEFT JOIN expositores ex ON ex.idexpositores = e.idexpositores
        ORDER BY e.idempresas DESC";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
    public function obtenerDatoEmpresas($where){
        $rowset = $this->tableGateway->select($where);
        return $rowset->current();
    }
    public function obtenerDatoCondicionEmpresas($idzonas=null){
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM empresas WHERE idzonas = {$idzonas} ORDER BY orden ASC";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
    public function obtenerZonasEmpresasPorFeria($idferias, $limit = 0){
        $condiciones = [];
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT z.orden AS zonaOrden, e.hash_url AS empresaHashUrl, e.hash_logo AS empresaHashLogo, e.orden AS empresaOrden
        FROM zonas z
        INNER JOIN empresas e ON e.idzonas = z.idzonas";
        if( $idferias != null ) $condiciones[] = "z.idferias = {$idferias}";
        if( !empty($condiciones) ) $sql .= " WHERE ".implode(" AND ", $condiciones);
        $sql .= " ORDER BY z.orden, e.orden ASC";
        if($limit > 0) {
            $sql .= " LIMIT {$limit}";
        }
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
    public function obtenerDatosEmpresas($idferias=null, $idzonas=null){
        $condiciones = [];
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT e.idempresas, e.nombre AS empresa, e.hash_logo, e.orden, z.idzonas, z.nombre AS zona, z.orden AS zona_orden
        FROM empresas e
        LEFT JOIN zonas z ON z.idzonas = e.idzonas";
        if( $idferias != null ) $condiciones[] = "z.idferias = {$idferias}";
        if( $idzonas != null ) $condiciones[] = "z.idzonas = {$idzonas}";
        if( !empty($condiciones) ) $sql .= " WHERE ".implode(" AND ", $condiciones);
        $sql .= " ORDER BY e.nombre ASC";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
    public function obtenerEmpresasPorIdZona($idzonas){
        $condiciones = [];
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM empresas e";
        $condiciones[] = "e.idzonas = {$idzonas}";
        if( !empty($condiciones) ) $sql .= " WHERE ".implode(" AND ", $condiciones);
        $sql .= " ORDER BY e.orden ASC";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
    public function obtenerEmpresaZonaInicial($idferias, $ordenzona){
        $condiciones = [];
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT z.orden AS ordenzona, e.*
        FROM zonas z
        INNER JOIN empresas e ON e.idzonas = z.idzonas";
        if( $idferias != null ) $condiciones[] = "z.idferias = {$idferias}";
        if( $ordenzona != null ) $condiciones[] = "z.orden = {$ordenzona}";
        if( !empty($condiciones) ) $sql .= " WHERE ".implode(" AND ", $condiciones);
        $sql .= " ORDER BY e.orden ASC";
        $sql .= " LIMIT 1";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->current();
        return $data;
    }
    public function actualizarDatosEmpresas($data,$idempresas){
        $rowset = $this->tableGateway->update($data,["idempresas" => $idempresas]);
    }
    public function agregarEmpresas($data){
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }
    public function eliminarEmpresas($idempresas){
        $this->tableGateway->delete(["idempresas" => $idempresas]);
    }
    public function obtenerFiltroDatosEmpresas($start,$length,$search=null,$totalregistro=false){
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM empresas";
        $sql .= " ORDER BY idempresas ASC";
        if(!$totalregistro)$sql .= " LIMIT {$start},{$length}";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
    public function obtenerEmpresasGalerias($idzonas){
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM empresas e
        LEFT JOIN stand_galeria sg ON sg.idstandgaleria = e.idstandgaleria";
        $sql .= " WHERE e.idzonas = {$idzonas}";
        $sql .= " ORDER BY e.orden ASC";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
}