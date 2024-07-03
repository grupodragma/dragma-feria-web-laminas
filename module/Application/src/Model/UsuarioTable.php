<?php

namespace Application\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Expression;

class UsuarioTable
{
    protected $tableGateway;

    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    public function obternerDatosUsuarios($idusuario)
    {
        $rowset = $this->tableGateway->select(['idusuario' => $idusuario]);
        return $rowset->current();
    }
    public function obternerDatosPorUsuario($usuario)
    {
        $rowset = $this->tableGateway->select(['usuario' => $usuario]);
        return $rowset->current();
    }
    public function actualizarDatosUsuarios($data, $idusuario)
    {
        $rowset = $this->tableGateway->update($data, ['idusuario' => $idusuario]);
    }
    public function verificarEmail($email)
    {
        $rowset = $this->tableGateway->select(['email' => $email]);
        return $rowset->current();
    }
    public function verificarUsuario($usuario)
    {
        $rowset = $this->tableGateway->select(['usuario' => $usuario]);
        return $rowset->current();
    }
    public function obtenerTodosUsuarios($idperfil = 'X')
    {
        $sqlSelect = $this->tableGateway->getSql()->select();
        $sqlSelect->join(['perfiles' => 'fd_perfiles'], 'perfiles.idperfil = fd_usuarios.idperfil', ['perfil' => 'nombre'], 'left');
        if ($idperfil != 'X') {
            $sqlSelect->where(['perfiles.idperfil' => $idperfil]);
        }
        $rowset = $this->tableGateway->selectWith($sqlSelect);
        return $rowset->toArray();
    }
    public function agregarUsuario($data)
    {
        $this->tableGateway->insert($data);
        return true;
    }
    public function eliminarUsuario($idusuario)
    {
        $this->tableGateway->delete(['idusuario' => $idusuario]);
    }
}
