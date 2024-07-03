<?php

namespace Application\Service;

use Laminas\Authentication\Result;
use Laminas\Session\Container;

class AuthManager
{
    private $authService;
    private $sessionManager;

    public function __construct($authService, $sessionManager)
    {
        $this->authService = $authService;
        $this->sessionManager = $sessionManager;
    }

    public function login($usuario, $password, $rememberMe)
    {
        $authAdapter = $this->authService->getAdapter();
        $authAdapter->setUser($usuario);
        $authAdapter->setPassword($password);
        $result = $this->authService->authenticate();
//$this->sessionManager->rememberMe(60*60*24*10);
        if ($result->getCode() == Result::SUCCESS && $rememberMe) {
            $this->sessionManager->rememberMe(60 * 60 * 24);
        }
        return $result;
    }

    public function logout()
    {
        $sessionUsuario = new Container('DatosSession', $this->sessionManager);
        $sessionUsuario->access = false;
        $this->authService->clearIdentity();
    }
}
