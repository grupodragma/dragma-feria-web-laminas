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
class AuthAdapter implements AdapterInterface {

    private $numeroDocumento;
    private $objVisitantesTable;
    private $sessionConteiner;
    private $salt = '::::::(`_´)::::: NCL/SECURE';
    private $objExpositoresTable;
    private $objUsuarioEventosTable;
    
    public function __construct($objVisitantesTable,$sessionConteiner,$objExpositoresTable,$objUsuarioEventosTable) {
        $this->objVisitantesTable = $objVisitantesTable;
        $this->sessionConteiner = $sessionConteiner;
        $this->objExpositoresTable = $objExpositoresTable;
        $this->objUsuarioEventosTable = $objUsuarioEventosTable;
    }
    
    public function setNumeroDocumento($numeroDocumento) {
        $this->numeroDocumento = trim((string)$numeroDocumento);
    }

    public function setIdFerias($idferias) {
        $this->idferias = trim((string)$idferias);
    }
    
    public function authenticate() {
        $acceso = $this->objVisitantesTable->obtenerDatoVisitantes(['idferias'=> $this->idferias,'numero_documento'=> $this->numeroDocumento]);
        if(!$acceso){
            $acceso = $this->objExpositoresTable->obtenerDatoExpositores(['idferias'=> $this->idferias,'numero_documento'=> $this->numeroDocumento]);
        }
        if($acceso){
            $this->objUsuarioEventosTable->agregarUsuarioEventosLogin($acceso, $this->idferias);
            $this->sessionConteiner->datosUsuario = $acceso;
            $this->sessionConteiner->access = true;
            return new Result(Result::SUCCESS, $acceso['nombres'], ['SUCCESS']);
        }else{
            return new Result(Result::FAILURE, null, ['CREDENTIAL_INVALID']);
        }

    }
}