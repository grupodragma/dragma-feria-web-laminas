<?php

namespace Application\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\Sql\Select;

class UsuarioEventosTable {
    protected $tableGateway;
    public function __construct(TableGatewayInterface $tableGateway) {
        $this->tableGateway = $tableGateway;
    }
    public function obtenerDatoUsuarioEventos($where){
        $rowset = $this->tableGateway->select($where);
        return $rowset->current();
    }
    public function actualizarDatosUsuarioEventos($data,$idusuarioeventos){
        $rowset = $this->tableGateway->update($data,["idusuarioeventos" => $idusuarioeventos]);
    }
    public function agregarUsuarioEventos($data){
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }
    public function eliminarUsuarioEventos($idusuarioeventos){
        $this->tableGateway->delete(["idusuarioeventos" => $idusuarioeventos]);
    }
    public function agregarUsuarioEventosLogin($data, $idferias=null){
        $data = [
            'idferias'=> $idferias,
            'url_referencia'=> $_SERVER['HTTP_REFERER'],
            'url_actual'=> '/registro',
            'url_click'=> '/login',
            'ip'=> $_SERVER['REMOTE_ADDR'],
            'fecha_registro'=> date('Y-m-d H:i:s'),
            'idvisitantes'=> ( $data['tipo'] == 'V' ) ? $data['idvisitantes'] : 0,
            'idexpositores'=> ( $data['tipo'] == 'E' ) ? $data['idexpositores'] : 0,
            'tipo_usuario'=> $data['tipo'],
            'user_agent'=>  $_SERVER['HTTP_USER_AGENT']
        ];
        $this->agregarUsuarioEventos($data);
    }
}