<?php

namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\View\Model\JsonModel;

class BuscadorController extends AbstractActionController
{
    protected $serviceManager;
    protected $objBuscadorTable;
    protected $objFeriasTable;
    protected $objEmpresasTable;

    public function __construct($serviceManager, $objBuscadorTable, $objFeriasTable, $objEmpresasTable)
    {
        $this->serviceManager = $serviceManager;
        $this->objBuscadorTable = $objBuscadorTable;
        $this->objFeriasTable = $objFeriasTable;
        $this->sessionContainer = $this->serviceManager->get('DatosSession')->datosUsuario;
        $this->objEmpresasTable = $objEmpresasTable;
    }

    public function buscadorAction()
    {
    }

    public function listarBuscadorAction()
    {
        $dataBuscador = $this->objBuscadorTable->obtenerBuscador($this->sessionContainer['idferias'], $this->sessionContainer['idperfil'], $this->sessionContainer['idusuario'], $this->sessionContainer['encargado']);
        $data_out = [];
        $data_out['data'] = [];
        foreach ($dataBuscador as $item) {
            $tipo = ($item['tipo'] === 'S') ? 'Speaker' : 'Producto';
            $data_out['data'][] = [
                $item['feria'],
                $tipo,
                $item['empresa'],
                $item['nombre'],
                $item['enlace'],
                $item['descripcion'],
                $item['usuario'],
                '<div class="clas btn btn-sm btn-info pop-up" href="/buscador/editar-buscador?idbuscador=' . $item['idbuscador'] . '"><i class="fas fa-pencil-alt"></i> <span class="hidden-xs">Editar</span></div> <div class="clas btn btn-sm btn-danger pop-up" href="/buscador/eliminar-buscador?idbuscador=' . $item['idbuscador'] . '"><i class="fas fa-times"></i> <span class="hidden-xs">Eliminar</span></div>'
            ];
        }
        return $this->jsonZF($data_out);
    }

    public function agregarBuscadorAction()
    {
        $data = [
            'ferias' => $this->objFeriasTable->obtenerFerias($this->sessionContainer['idferias'], $this->sessionContainer['idperfil']),
            'empresas' => $this->objEmpresasTable->obtenerEmpresas($this->sessionContainer['idferias'], $this->sessionContainer['idperfil'], $this->sessionContainer['idempresas'])
        ];
        return $this->consoleZF($data);
    }

    public function guardarAgregarBuscadorAction()
    {
        $imagenLogo = $this->params()->fromFiles('imagen');
        $datosFormulario = $this->params()->fromPost();
        $idferiasSelected = (isset($datosFormulario['idferias'])) ? $datosFormulario['idferias'] : $this->sessionContainer['idferias'];
        //$enlace = ($datosFormulario['idempresas'] === '') ? $datosFormulario['enlace'] : '';
        $data = [
            'idferias' => $idferiasSelected,
            'tipo' => @$datosFormulario['tipo'],
            'idempresas' => $datosFormulario['idempresas'],
            'nombre' => $datosFormulario['nombre'],
            'enlace' => $datosFormulario['enlace'],
            'descripcion' => $datosFormulario['descripcion'],
            'enlace_wsp' => $datosFormulario['enlace_wsp'],
            'idusuario' => $this->sessionContainer['idusuario'],
            'pagina_envivo' => (isset($datosFormulario['pagina_envivo'])) ? $datosFormulario['pagina_envivo'] : 0
        ];

        ////////// SCRIPT PARA GUARDAR IMAGEN [INICIO] //////////
        $directorioLogo = getcwd() . '/public/buscador/imagen';
        $datosImagenLogo = [];
        $datosImagenLogo['id'] = md5(uniqid());
        if ($imagenLogo['size'] !== 0) {
            $datosImagenLogo['extension'] = strtolower(pathinfo($imagenLogo['name'])['extension']);
            if (in_array($datosImagenLogo['extension'], ['jpg','jpeg','png','gif','svg'])) {
                $datosImagenLogo['nombre_completo'] = $datosImagenLogo['id'] . '.' . $datosImagenLogo['extension'];
                $datosImagenLogo['nombre_original'] = $imagenLogo['name'];
                if (move_uploaded_file($imagenLogo['tmp_name'], $directorioLogo . '/' . $datosImagenLogo['nombre_completo'])) {
                    $data['hash_imagen'] = $datosImagenLogo['nombre_completo'];
                    $data['nombre_imagen'] = $datosImagenLogo['nombre_original'];
                }
            }
        }
        ////////// SCRIPT PARA GUARDAR IMAGEN [FIN] //////////

        $this->objBuscadorTable->agregarBuscador($data);
        return $this->jsonZF(['result' => 'success']);
    }

    public function editarBuscadorAction()
    {
        $idbuscador = $this->params()->fromQuery('idbuscador');
        $dataBuscador = $this->objBuscadorTable->obtenerDatoBuscador(['idbuscador' => $idbuscador]);
        $dataBuscador['ferias'] = $this->objFeriasTable->obtenerFerias($this->sessionContainer['idferias'], $this->sessionContainer['idperfil']);
        $dataBuscador['empresas'] = $this->objEmpresasTable->obtenerEmpresas($this->sessionContainer['idferias'], $this->sessionContainer['idperfil'], $this->sessionContainer['idempresas']);
        return $this->consoleZF($dataBuscador);
    }

    public function guardarEditarBuscadorAction()
    {
        $imagenLogo = $this->params()->fromFiles('imagen');
        $idbuscador = $this->params()->fromQuery('idbuscador');
        $datosFormulario = $this->params()->fromPost();
        $idferiasSelected = (isset($datosFormulario['idferias'])) ? $datosFormulario['idferias'] : $this->sessionContainer['idferias'];
        //$enlace = ($datosFormulario['idempresas'] === '') ? $datosFormulario['enlace'] : '';
        $data = [
            'idferias' => $idferiasSelected,
            'tipo' => @$datosFormulario['tipo'],
            'idempresas' => $datosFormulario['idempresas'],
            'nombre' => $datosFormulario['nombre'],
            'enlace' => $datosFormulario['enlace'],
            'descripcion' => $datosFormulario['descripcion'],
            'enlace_wsp' => $datosFormulario['enlace_wsp'],
            'idusuario' => $this->sessionContainer['idusuario'],
            'pagina_envivo' => (isset($datosFormulario['pagina_envivo'])) ? $datosFormulario['pagina_envivo'] : 0
        ];
        ////////// SCRIPT PARA ACTUALIZAR IMAGEN [INICIO] //////////
        $directorioLogo = getcwd() . '/public/buscador/imagen';
        $datosImagenLogo = [];
        $datosImagenLogo['id'] = md5(uniqid());
        if (! empty($imagenLogo['name']) && $imagenLogo['size'] !== 0) {
            $dataBuscador = $this->objBuscadorTable->obtenerDatoBuscador(['idbuscador' => $idbuscador]);
            if ($dataBuscador) {
                if (file_exists($directorioLogo . '/' . $dataBuscador['hash_imagen'])) {
                    @unlink($directorioLogo . '/' . $dataBuscador['hash_imagen']);
                }
                $datosImagenLogo['extension'] = strtolower(pathinfo($imagenLogo['name'])['extension']);
                if (in_array($datosImagenLogo['extension'], ['jpg','jpeg','png','gif','svg'])) {
                    $datosImagenLogo['nombre_completo'] = $datosImagenLogo['id'] . '.' . $datosImagenLogo['extension'];
                    $datosImagenLogo['nombre_original'] = $imagenLogo['name'];
                    if (move_uploaded_file($imagenLogo['tmp_name'], $directorioLogo . '/' . $datosImagenLogo['nombre_completo'])) {
                        $data['hash_imagen'] = $datosImagenLogo['nombre_completo'];
                        $data['nombre_imagen'] = $datosImagenLogo['nombre_original'];
                    }
                }
            }
        }
        ////////// SCRIPT PARA ACTUALIZAR IMAGEN [FIN] //////////
        $this->objBuscadorTable->actualizarDatosBuscador($data, $idbuscador);
        return $this->jsonZF(['result' => 'success']);
    }

    public function eliminarBuscadorAction()
    {
        $idbuscador = $this->params()->fromQuery('idbuscador');
        $dataBuscador = $this->objBuscadorTable->obtenerDatoBuscador(['idbuscador' => $idbuscador]);
        return $this->consoleZF($dataBuscador);
    }

    public function confirmarEliminarBuscadorAction()
    {
        $idbuscador = $this->params()->fromQuery('idbuscador');
        $dataBuscador = $this->objBuscadorTable->obtenerDatoBuscador(['idbuscador' => $idbuscador]);
        $directorioLogo = getcwd() . '/public/buscador/imagen';
        if ($dataBuscador) {
            if (file_exists($directorioLogo . '/' . $dataBuscador['hash_imagen'])) {
                @unlink($directorioLogo . '/' . $dataBuscador['hash_imagen']);
            }
        }
        $this->objBuscadorTable->eliminarBuscador($idbuscador);
        return $this->jsonZF(['result' => 'success']);
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
