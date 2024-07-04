<?php

namespace Application\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\Sql\Select;

class CronogramasTable {
    protected $tableGateway;
    public function __construct(TableGatewayInterface $tableGateway) {
        $this->tableGateway = $tableGateway;
    }
    public function obtenerCronogramas(){
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT c.*, f.nombre as feria, CONCAT(e.nombres, ' ', e.apellido_paterno, ' ', e.apellido_materno) AS expositor FROM cronogramas c
        LEFT JOIN ferias f ON f.idferias = c.idferias
        LEFT JOIN expositores e ON e.idexpositores = c.idexpositores
        ORDER BY c.idcronogramas DESC";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
    public function obtenerDatoCronogramas($where){
        $rowset = $this->tableGateway->select($where);
        return $rowset->current();
    }
    public function actualizarDatosCronogramas($data,$idcronogramas){
        $rowset = $this->tableGateway->update($data,["idcronogramas" => $idcronogramas]);
    }
    public function agregarCronogramas($data){
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }
    public function eliminarCronogramas($idcronogramas){
        $this->tableGateway->delete(["idcronogramas" => $idcronogramas]);
    }
    public function obtenerFiltroDatosCronogramas($start,$length,$search=null,$totalregistro=false){
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM cronogramas";
        $sql .= " ORDER BY idcronogramas ASC";
        if(!$totalregistro)$sql .= " LIMIT {$start},{$length}";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
    public function obtenerCronogramasAgrupados($idferias){
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT *, IF(conferencias_fecha_inicio <= NOW(), 1, 0) AS estado_conferencia FROM (
            SELECT c.*, CONCAT(e.nombres,' ', e.apellido_paterno,' ', e.apellido_materno) AS expositor,
            CONCAT(c.fecha, ' ', c.hora_inicio) AS conferencias_fecha_inicio, CONCAT(c.fecha, ' ', c.hora_fin) AS conferencias_fecha_fin
            FROM cronogramas c
            LEFT JOIN expositores e On e.idexpositores = c.idexpositores
            WHERE c.idferias = {$idferias}
        ) AS cr
        ORDER BY conferencias_fecha_inicio ASC";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        $response = [];
        if(!empty($data)){
            foreach($data as $item) {
                $response[$item['fecha']][] = $item;
            }
        }
        return $response;
    }
    public function obtenerCronogramaFechaActual($idferias){
        $adapter = $this->tableGateway->getAdapter();
        $sql = 'SELECT cn.*, cf.titulo AS conferencia, cf.enlace AS conferencia_enlace, cf.titulo_chat_conferencia AS titulo_chat_conferencia FROM (
            SELECT *, UNIX_TIMESTAMP(CONCAT(fecha, " ", hora_inicio)) AS UNIX_TIMESTAMP_INICIO, UNIX_TIMESTAMP(CONCAT(fecha, " ", hora_fin)) AS UNIX_TIMESTAMP_FIN
            FROM cronogramas
            ORDER BY fecha, hora_inicio, hora_fin ASC
        ) AS cn
        INNER JOIN conferencias cf ON cf.idconferencias = cn.idconferencias
        WHERE NOW() BETWEEN FROM_UNIXTIME(UNIX_TIMESTAMP_INICIO) AND FROM_UNIXTIME(UNIX_TIMESTAMP_FIN) AND cn.idferias = '.$idferias;
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->current();
        return $data;
    }
    public function obtenerCronogramaPorId($idcronogramas){
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT cn.*, cf.titulo AS conferencia, cf.enlace AS conferencia_enlace, cf.titulo_chat_conferencia AS titulo_chat_conferencia
        FROM cronogramas cn
        INNER JOIN conferencias cf ON cf.idconferencias = cn.idconferencias
        WHERE cn.idcronogramas = {$idcronogramas}";
        return $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->current();
    }
}