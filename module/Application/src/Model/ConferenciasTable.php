<?php

namespace Application\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\Sql\Select;

class ConferenciasTable {
    protected $tableGateway;
    public function __construct(TableGatewayInterface $tableGateway) {
        $this->tableGateway = $tableGateway;
    }
    public function obtenerConferencias($idferias, $idperfil){
        $condiciones = [];
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT c.*, f.nombre AS feria FROM conferencias c
        LEFT JOIN ferias f ON f.idferias = c.idferias";
        if( $idperfil != 1 && $idferias != null ) $condiciones[] = "f.idferias = {$idferias}";
        if( !empty($condiciones) ) $sql .= " WHERE ".implode(" AND ", $condiciones);
        $sql .= " ORDER BY c.idconferencias DESC";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
    public function obtenerDatoConferencias($where){
        $rowset = $this->tableGateway->select($where);
        return $rowset->current();
    }
    public function obtenerDatosConferencias($idferias){
        $condiciones = [];
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM conferencias c";
        if( $idferias != null ) $condiciones[] = "c.idferias = {$idferias}";
        if( !empty($condiciones) ) $sql .= " WHERE ".implode(" AND ", $condiciones);
        $sql .= " ORDER BY c.idconferencias DESC";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
    public function obtenerConferenciasProgramados($idferias){
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM (
            SELECT cf.*, c.fecha, c.hora_inicio, c.hora_fin, CONCAT(c.fecha, ' ', c.hora_inicio) AS fecha_inicio, CONCAT(c.fecha, ' ', c.hora_fin) AS fecha_fin
            FROM cronogramas c
            INNER JOIN conferencias cf ON cf.idconferencias = c.idconferencias
            WHERE cf.idferias = {$idferias} AND c.fecha != '' AND c.hora_inicio != '' AND c.hora_fin != '' AND cf.enlace != ''
        ) AS conf
        WHERE fecha_inicio <= NOW()
        ORDER BY fecha_inicio ASC";
        return $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
    }
    public function actualizarDatosConferencias($data,$idconferencias){
        $rowset = $this->tableGateway->update($data,["idconferencias" => $idconferencias]);
    }
    public function agregarConferencias($data){
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }
    public function eliminarConferencias($idconferencias){
        $this->tableGateway->delete(["idconferencias" => $idconferencias]);
    }
    public function obtenerFiltroDatosConferencias($start,$length,$search=null,$totalregistro=false){
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM conferencias";
        $sql .= " ORDER BY idconferencias ASC";
        if(!$totalregistro)$sql .= " LIMIT {$start},{$length}";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
}