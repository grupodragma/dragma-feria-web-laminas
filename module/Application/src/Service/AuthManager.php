<?php
namespace Application\Service;
use Laminas\Authentication\Result;
use Laminas\Session\Container;

class AuthManager {
    
    private $authService;
    private $sessionManager;
    
    public function __construct($authService, $sessionManager) {
        $this->authService = $authService;
        $this->sessionManager = $sessionManager;
    }

    public function login($numeroDocumento, $idferias) {   
        $authAdapter = $this->authService->getAdapter();
        $authAdapter->setNumeroDocumento($numeroDocumento);
        $authAdapter->setIdFerias($idferias);
        $result = $this->authService->authenticate();
        if ($result->getCode()==Result::SUCCESS) {
            $this->sessionManager->rememberMe(60*60*24);
        }
        return $result;
    }
    
    public function logout() {
        $sessionUsuario = new Container('DatosSession', $this->sessionManager);
        $sessionUsuario->access = false;
        $this->authService->clearIdentity();
        session_unset();             
    }
}