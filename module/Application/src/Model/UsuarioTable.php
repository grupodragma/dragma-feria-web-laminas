<?php
namespace Application\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Expression;

class UsuarioTable {
    
    protected $tableGateway;

    public function __construct(TableGatewayInterface $tableGateway) {
        $this->tableGateway = $tableGateway;
    }
    public function obternerDatosUsuarios(){
        $rowset = $this->tableGateway->select([]);
        return $rowset->toArray();
    }
    public function obternerDatoUsuario($where){
        $rowset = $this->tableGateway->select($where);
        return $rowset->current();
    }
    public function actualizarDatosUsuarios($data, $idusuario){
        $rowset = $this->tableGateway->update($data, ['idusuario' => $idusuario]);
    }
    public function verificarEmail($email){
        $rowset = $this->tableGateway->select(['email' => $email]);
        return $rowset->current();
    }
    public function verificarUsuario($usuario){
        $rowset = $this->tableGateway->select(['usuario' => $usuario]);
        return $rowset->current();
    }
    public function agregarUsuario($data){
        $this->tableGateway->insert($data);
        return true;
    }
    public function eliminarUsuario($idusuario){
        $this->tableGateway->delete(['idusuario' => $idusuario]);
    }
}

