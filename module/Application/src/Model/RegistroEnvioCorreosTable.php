<?php

namespace Application\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\Sql\Select;

class RegistroEnvioCorreosTable {
    protected $tableGateway;
    public function __construct(TableGatewayInterface $tableGateway) {
        $this->tableGateway = $tableGateway;
    }
    public function obtenerRegistroEnvioCorreos(){
        $select = $this->tableGateway->getSql()->select();
        $select->order('idregistroenviocorreos DESC');
        $rowset = $this->tableGateway->selectWith($select);
        return $rowset->toArray();
    }
    public function obtenerDatoRegistroEnvioCorreos($where){
        $rowset = $this->tableGateway->select($where);
        return $rowset->current();
    }
    public function obtenerDatosRegistroEnvioCorreos($where){
        $rowset = $this->tableGateway->select($where);
        return $rowset->toArray();
    }
    public function actualizarDatosRegistroEnvioCorreos($data,$idregistroenviocorreos){
        $rowset = $this->tableGateway->update($data,["idregistroenviocorreos" => $idregistroenviocorreos]);
    }
    public function agregarRegistroEnvioCorreos($data){
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }
    public function eliminarRegistroEnvioCorreos($idregistroenviocorreos){
        $this->tableGateway->delete(["idregistroenviocorreos" => $idregistroenviocorreos]);
    }
    public function obtenerFiltroDatosRegistroEnvioCorreos($start,$length,$search=null,$totalregistro=false){
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM registroenviocorreos";
        $sql .= " ORDER BY idregistroenviocorreos ASC";
        if(!$totalregistro)$sql .= " LIMIT {$start},{$length}";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
}