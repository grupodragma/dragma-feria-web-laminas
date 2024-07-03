<?php

namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\View\Model\JsonModel;

class NetworkingController extends AbstractActionController
{
    protected $serviceManager;
    protected $sessionContainer;

    public function __construct($serviceManager)
    {
        $this->serviceManager = $serviceManager;
        $this->sessionContainer = $this->serviceManager->get('DatosSession')->datosUsuario;
    }

    public function expositoresAction()
    {
    }

    private function consoleZF($data)
    {
        $viewModel = new ViewModel($data);
        $viewModel->setTerminal(true);
        return $viewModel;
    }

    private function jsonZF($data)
    {
        return new JsonModel($data);
    }
}
