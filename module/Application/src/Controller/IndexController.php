<?php

/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    protected $serviceManager;
    protected $objAccesoTable;
    public function __construct($serviceManager, $objAccesoTable)
    {
        $this->serviceManager = $serviceManager;
        $this->objAccesoTable = $objAccesoTable;
    }

    public function indexAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            echo '<script>document.location = "/";</script>';
            die;
        }
        $config = $this->serviceManager->get('Config');
        $tiempo_bloqueo = (! empty($config['secure_access']['tiempo_bloqueo'])) ? $config['secure_access']['tiempo_bloqueo'] : 30;
        $this->layout()->setTemplate('layout/login');
        return new ViewModel(['tiempo_bloqueo' => $tiempo_bloqueo]);
    }
}
