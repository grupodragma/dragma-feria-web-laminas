<?php

namespace Application\Service;

use Laminas\Authentication\Adapter\AdapterInterface;
use Laminas\Authentication\Result;
use Laminas\Crypt\Password\Bcrypt;

/**
 * Adapter used for authenticating user. It takes login and password on input
 * and checks the database if there is a user with such login (email) and password.
 * If such user exists, the service returns his identity (email). The identity
 * is saved to session and can be retrieved later with Identity view helper provided
 * by ZF3.
 */
class AuthAdapter implements AdapterInterface
{
    private $usuario;
    private $password;
    private $accesoTable;
    private $sessionConteiner;
    private $salt = '::::::(`_´)::::: NCL/SECURE';

    public function __construct($accesoTable, $sessionConteiner)
    {
        $this->accesoTable = $accesoTable;
        $this->sessionConteiner = $sessionConteiner;
    }

    public function setUser($usuario)
    {
        $this->usuario = trim($usuario);
    }

    public function setPassword($password)
    {
        $this->password = trim((string)$password);
    }

    public function authenticate()
    {

        $acceso = $this->accesoTable->verificarUsuario($this->usuario, $this->password);
        if ($acceso !== false) {
            if ($acceso['estado'] == 'A') {
                $this->sessionConteiner->datosUsuario = $acceso;
                $this->sessionConteiner->access = true;
                return new Result(Result::SUCCESS, $this->usuario, ['SUCCESS']);
            } else {
                return new Result(Result::FAILURE, $this->usuario, ['NOACTIVATE']);
            }
        } else {
            return new Result(Result::FAILURE, null, ['CREDENTIAL_INVALID']);
        }
    }
}
