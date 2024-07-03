<?php

namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\View\Model\JsonModel;

class PlanoController extends AbstractActionController
{
    protected $serviceManager;
    protected $objPlanosTable;
    protected $sessionContainer;
    protected $objEmpresasTable;
    protected $imagenExtensionesValidas;

    public function __construct($serviceManager, $objPlanosTable, $objEmpresasTable)
    {
        $this->serviceManager = $serviceManager;
        $this->objPlanosTable = $objPlanosTable;
        $this->sessionContainer = $this->serviceManager->get('DatosSession')->datosUsuario;
        $this->objEmpresasTable = $objEmpresasTable;
        $this->imagenExtensionesValidas = ['jpg','jpeg','png'];
    }

    public function planosAction()
    {
    }

    public function listarPlanosAction()
    {
        $dataPlanos = $this->objPlanosTable->obtenerPlanos($this->sessionContainer['idperfil'], $this->sessionContainer['idferias'], $this->sessionContainer['idusuario'], $this->sessionContainer['encargado']);
        $data_out = [];
        $data_out['data'] = [];
        foreach ($dataPlanos as $item) {
            $data_out['data'][] = [
                $item['empresa'],
                $item['nombre'],
                '<div class="clas btn btn-sm btn-info pop-up-2" href="/plano/editar-planos?idplanos=' . $item['idplanos'] . '"><i class="fas fa-pencil-alt"></i> <span class="hidden-xs">Editar</span></div> <div class="clas btn btn-sm btn-danger pop-up-2" href="/plano/eliminar-planos?idplanos=' . $item['idplanos'] . '"><i class="fas fa-times"></i> <span class="hidden-xs">Eliminar</span></div>'
            ];
        }
        return $this->jsonZF($data_out);
    }

    public function agregarPlanosAction()
    {
        $data = [
            'empresas' => $this->objEmpresasTable->obtenerEmpresas($this->sessionContainer['idferias'], $this->sessionContainer['idperfil'], $this->sessionContainer['idempresas'])
        ];
        return $this->consoleZF($data);
    }

    public function guardarAgregarPlanosAction()
    {
        $archivos = $this->params()->fromFiles();
        $datosFormulario = $this->params()->fromPost();
        $data = [
            'idempresas' => $datosFormulario['idempresas'],
            'nombre' => $datosFormulario['nombre'],
            'descripcion' => $datosFormulario['descripcion'],
            'enlace_wsp' => $datosFormulario['enlace_wsp'],
            'informacion' => $datosFormulario['informacion'],
            'enlace' => $datosFormulario['enlace'],
            'tipo_enlace' => @$datosFormulario['tipo_enlace'],
            'idusuario' => $this->sessionContainer['idusuario'],
        ];
        ////////// SCRIPT PARA GUARDAR IMAGEN [INICIO] //////////
        $imagenExtensionesValidas = [];
        $carpetaProductoArchivos = '';
        $datosProductoArchivo = [];
        $keyDataArchivo = [];
        if (! empty($archivos)) {
            foreach ($archivos as $key => $archivo) {
                if ($key === 'imagenes') {
                    continue;
                }
                switch ($key) {
                    case 'archivo_pdf':
                        $imagenExtensionesValidas = ['pdf'];
                        $carpetaProductoArchivos = getcwd() . '/public/planos/documentos';
                        $keyDataArchivo[$key] = ['hash' => 'hash_pdf', 'nombre' => 'nombre_pdf'];
                        if ($archivo['size'] > 0 && $archivo['size'] > 5000000) {
                            return $this->jsonZF(['result' => 'file_max_size']);
                        }
                        break;
                    case 'imagen':
                        $imagenExtensionesValidas = $this->imagenExtensionesValidas;
                        $carpetaProductoArchivos = getcwd() . '/public/planos/imagen';
                        $keyDataArchivo[$key] = ['hash' => 'hash_imagen', 'nombre' => 'nombre_imagen'];
                        break;
                }
                $datosProductoArchivo['id'] = md5(uniqid());
                if ($archivo['size'] !== 0) {
                    $datosProductoArchivo['extension'] = strtolower(pathinfo($archivo['name'])['extension']);
                    if (in_array($datosProductoArchivo['extension'], $imagenExtensionesValidas)) {
                        $datosProductoArchivo['nombre_completo'] = $datosProductoArchivo['id'] . '.' . $datosProductoArchivo['extension'];
                        $datosProductoArchivo['nombre_original'] = $archivo['name'];
                        if (move_uploaded_file($archivo['tmp_name'], $carpetaProductoArchivos . '/' . $datosProductoArchivo['nombre_completo'])) {
                            $data[$keyDataArchivo[$key]['hash']] = $datosProductoArchivo['nombre_completo'];
                            $data[$keyDataArchivo[$key]['nombre']] = $datosProductoArchivo['nombre_original'];
                        }
                    }
                }
            }
        }
        ////////// SCRIPT PARA GUARDAR IMAGEN [FIN] //////////
        $this->objPlanosTable->agregarPlanos($data);
        return $this->jsonZF(['result' => 'success']);
    }

    public function editarPlanosAction()
    {
        $idplanos = $this->params()->fromQuery('idplanos');
        $dataPlanos = $this->objPlanosTable->obtenerDatoPlanos(['idplanos' => $idplanos]);
        $dataPlanos['empresas'] = $this->objEmpresasTable->obtenerEmpresas($this->sessionContainer['idferias'], $this->sessionContainer['idperfil'], $this->sessionContainer['idempresas']);
        return $this->consoleZF($dataPlanos);
    }

    public function guardarEditarPlanosAction()
    {
        $archivos = $this->params()->fromFiles();
        $idplanos = $this->params()->fromQuery('idplanos');
        $datosFormulario = $this->params()->fromPost();
        $data = [
            'idempresas' => $datosFormulario['idempresas'],
            'nombre' => $datosFormulario['nombre'],
            'descripcion' => $datosFormulario['descripcion'],
            'enlace_wsp' => $datosFormulario['enlace_wsp'],
            'informacion' => $datosFormulario['informacion'],
            'enlace' => $datosFormulario['enlace'],
            'tipo_enlace' => @$datosFormulario['tipo_enlace'],
            'idusuario' => $this->sessionContainer['idusuario'],
        ];
        ////////// SCRIPT PARA ACTUALIZAR IMAGEN [INICIO] //////////
        $imagenExtensionesValidas = [];
        $carpetaProductoArchivos = '';
        $datosProductoArchivo = [];
        $keyDataArchivo = [];
        if (! empty($archivos)) {
            foreach ($archivos as $key => $archivo) {
                switch ($key) {
                    case 'archivo_pdf':
                        $imagenExtensionesValidas = ['pdf'];
                        $carpetaProductoArchivos = getcwd() . '/public/planos/documentos';
                        $keyDataArchivo[$key] = ['hash' => 'hash_pdf', 'nombre' => 'nombre_pdf'];
                        if ($archivo['size'] > 0 && $archivo['size'] > 5000000) {
                            return $this->jsonZF(['result' => 'file_max_size']);
                        }
                        break;
                    case 'imagen':
                        $imagenExtensionesValidas = $this->imagenExtensionesValidas;
                        $carpetaProductoArchivos = getcwd() . '/public/planos/imagen';
                        $keyDataArchivo[$key] = ['hash' => 'hash_imagen', 'nombre' => 'nombre_imagen'];
                        break;
                }
                $datosProductoArchivo['id'] = md5(uniqid());
                if (! empty($archivo['name']) && $archivo['size'] !== 0) {
                    $dataPlano = $this->objPlanosTable->obtenerDatoPlanos(['idplanos' => $idplanos]);
                    if ($dataPlano) {
                        if (file_exists($carpetaProductoArchivos . '/' . $dataPlano[$keyDataArchivo[$key]['hash']])) {
                            @unlink($carpetaProductoArchivos . '/' . $dataPlano[$keyDataArchivo[$key]['hash']]);
                        }
                        $datosProductoArchivo['extension'] = strtolower(pathinfo($archivo['name'])['extension']);
                        if (in_array($datosProductoArchivo['extension'], $imagenExtensionesValidas)) {
                            $datosProductoArchivo['nombre_completo'] = $datosProductoArchivo['id'] . '.' . $datosProductoArchivo['extension'];
                            $datosProductoArchivo['nombre_original'] = $archivo['name'];
                            if (move_uploaded_file($archivo['tmp_name'], $carpetaProductoArchivos . '/' . $datosProductoArchivo['nombre_completo'])) {
                                $data[$keyDataArchivo[$key]['hash']] = $datosProductoArchivo['nombre_completo'];
                                $data[$keyDataArchivo[$key]['nombre']] = $datosProductoArchivo['nombre_original'];
                            }
                        }
                    }
                }
            }
        }
        ////////// SCRIPT PARA ACTUALIZAR IMAGEN [FIN] //////////
        $this->objPlanosTable->actualizarDatosPlanos($data, $idplanos);
        return $this->jsonZF(['result' => 'success']);
    }

    public function eliminarPlanosAction()
    {
        $idplanos = $this->params()->fromQuery('idplanos');
        $dataPlanos = $this->objPlanosTable->obtenerDatoPlanos(['idplanos' => $idplanos]);
        return $this->consoleZF($dataPlanos);
    }

    public function confirmarEliminarPlanosAction()
    {
        $idplanos = $this->params()->fromQuery('idplanos');
        $dataPlanos = $this->objPlanosTable->obtenerDatoPlanos(['idplanos' => $idplanos]);
        $directorioImagen = getcwd() . '/public/planos/imagen';
        if ($dataPlanos) {
            $dataArchivos = [
                'pdf' => ['directorio' => getcwd() . '/public/planos/documentos'],
                'imagen' => ['directorio' => getcwd() . '/public/planos/imagen']
            ];
            foreach ($dataArchivos as $key => $item) {
                if (isset($dataPlanos['hash_' . $key]) && file_exists($item['directorio'] . '/' . $dataPlanos['hash_' . $key])) {
                    @unlink($item['directorio'] . '/' . $dataPlanos['hash_' . $key]);
                }
            }
        }
        $this->objPlanosTable->eliminarPlanos($idplanos);
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
