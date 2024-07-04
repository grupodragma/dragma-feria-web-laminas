<?php

namespace Application\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\Sql\Select;

class AgendaVirtualTable {
    protected $tableGateway;
    public function __construct(TableGatewayInterface $tableGateway) {
        $this->tableGateway = $tableGateway;
    }
    public function obtenerAgendaVirtual(){
        $select = $this->tableGateway->getSql()->select();
        $select->order('idagendavirtual DESC');
        $rowset = $this->tableGateway->selectWith($select);
        return $rowset->toArray();
    }
    public function obtenerDatoAgendaVirtual($where){
        $rowset = $this->tableGateway->select($where);
        return $rowset->current();
    }
    public function actualizarDatosAgendaVirtual($data,$idagendavirtual){
        $rowset = $this->tableGateway->update($data,["idagendavirtual" => $idagendavirtual]);
    }
    public function agregarAgendaVirtual($data){
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }
    public function eliminarAgendaVirtual($idagendavirtual){
        $this->tableGateway->delete(["idagendavirtual" => $idagendavirtual]);
    }
    public function obtenerFiltroDatosAgendaVirtual($start,$length,$search=null,$totalregistro=false){
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM planes";
        $sql .= " ORDER BY idagendavirtual ASC";
        if(!$totalregistro)$sql .= " LIMIT {$start},{$length}";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
    function validarRangoAgendaVirtual($dateFromUser) {
        $dateSelectedUser = date('Y-m-d', strtotime(str_replace("/","-", $dateFromUser)));
        $startDate = $dateSelectedUser." 09:00";
        $endDate = $dateSelectedUser." 20:00";
        $dataUser = str_replace("/","-", $dateFromUser);
        $start = strtotime($startDate);
        $end = strtotime($endDate);
        $check = strtotime($dataUser);
        return (($check >= $start) && ($check <= $end));
    }
    public function obtenerAgendaVirtualPorFecha($fecha, $idempresas){
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT *
        FROM agenda_virtual
        WHERE DATE(fecha_agenda_inicio) = '{$fecha}' AND idempresas = {$idempresas}
        ORDER BY fecha_agenda_inicio ASC";
        return $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
    }
    public function obtenerAgendaVirtualPorFechaSinEmpresa($fecha){
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT *
        FROM agenda_virtual
        WHERE DATE(fecha_agenda_inicio) = '{$fecha}'
        ORDER BY fecha_agenda_inicio ASC";
        return $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
    }
    public function obtenerAgendaUsuarioPorEmpresa($idferias, $idusuario, $tipo_usuario, $fecha, $idempresas){
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT *
        FROM agenda_virtual
        WHERE idferias = {$idferias} AND idusuario = {$idusuario} AND tipo_usuario = '{$tipo_usuario}' AND idempresas = {$idempresas} AND '{$fecha}' BETWEEN fecha_agenda_inicio AND fecha_agenda_fin
        ORDER BY fecha_agenda_inicio ASC";
        return $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
    }
    public function obtenerAgendaUsuario($idferias, $idusuario, $tipo_usuario, $fecha){
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT *
        FROM agenda_virtual
        WHERE idferias = {$idferias} AND idusuario = {$idusuario} AND tipo_usuario = '{$tipo_usuario}' AND '{$fecha}' BETWEEN fecha_agenda_inicio AND fecha_agenda_fin
        ORDER BY fecha_agenda_inicio ASC";
        return $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
    }
}