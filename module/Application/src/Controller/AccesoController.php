<?php

/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\View\Model\JsonModel;
use Application\Model\AccesoTable;

class AccesoController extends AbstractActionController
{
    private $authManager;
    private $objAcceso;
    private $salt = '::::::(`_´)::::: NCL/SECURE';

    public function __construct($authManager, AccesoTable $objAcceso)
    {
        $this->authManager = $authManager;
        $this->objAcceso = $objAcceso;
    }

    public function loginAction()
    {
        if ($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $data['remember_me'] = (! isset($data['remember_me'])) ? false : true;
            $result = $this->authManager->login($data['usuario'], $data['contrasena'], $data['remember_me']);
            $estatus = $result->getMessages();
            $estatus = $estatus[0];
            if ($estatus == 'SUCCESS') {
                return new JsonModel(['result' => 'success']);
            } elseif ($estatus == 'BANNED') {
                return new JsonModel(['result' => 'excess_error']);
            } elseif ($estatus == 'NOACTIVATE') {
                return new JsonModel(['result' => 'noactivate']);
            } elseif ($estatus == 'CREDENTIAL_INVALID') {
                return new JsonModel(['result' => 'credential_invalid']);
            } else {
                return new JsonModel(['result' => 'error']);
            }
        } else {
            $this->getResponse()->setStatusCode(404);
            return;
        }
    }

    public function logoutAction()
    {
        $this->authManager->logout();
        return $this->redirect()->toRoute('login');
    }
}
