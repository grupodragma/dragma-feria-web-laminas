<?php

namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\View\Model\JsonModel;

class SistemaController extends AbstractActionController
{
    protected $serviceManager;
    protected $objUsuarioTable;
    protected $objPerfilesTable;
    private $salt = '::::::(`_´)::::: NCL/SECURE';
    protected $dir_avatar;
    protected $uploaderService;
    protected $objModulosTable;
    protected $objSubModulosTable;
    protected $objFeriasTable;

    public function __construct($serviceManager, $objUsuarioTable, $objPerfilesTable, $uploaderService, $objModulosTable, $objSubModulosTable, $objFeriasTable)
    {
        $this->serviceManager = $serviceManager;
        $this->objUsuarioTable = $objUsuarioTable;
        $this->objPerfilesTable = $objPerfilesTable;
        $this->dir_avatar = '/img/avatars/';
        $this->uploaderService = $uploaderService;
        $this->objModulosTable = $objModulosTable;
        $this->objSubModulosTable = $objSubModulosTable;
        $this->objFeriasTable = $objFeriasTable;
        $this->sessionContainer = $this->serviceManager->get('DatosSession')->datosUsuario;
    }

    public function usuariosAction()
    {
    }

    public function listarUsuariosAction()
    {
        $data_usuarios = $this->objUsuarioTable->obtenerTodosUsuarios();
        $data_out = [];
        $data_out['data'] = [];
        foreach ($data_usuarios as $usuario) {
            $idferias = $usuario['idferias'];
            $ferias = $this->objFeriasTable->obtenerDatoFerias(["idferias" => $idferias]);
            $disabledUserAdmin = ( $usuario['idusuario'] == '1') ? "disabled" : "";
            $data_out['data'][] = [
                $usuario['nombres'],
                $usuario['apellido_paterno'],
                $usuario['apellido_materno'],
                $usuario['email'],
                $usuario['perfil'],
                isset($ferias['nombre']) ? $ferias['nombre'] : '',
                '<div class="clas btn btn-sm btn-info pop-up" href="/sistema/editar-usuario?idusuario=' . $usuario['idusuario'] . '"><i class="fas fa-pencil-alt"></i> <span class="hidden-xs">Editar</span></div> <div class="clas btn btn-sm btn-danger pop-up ' . $disabledUserAdmin . '" href="/sistema/eliminar-usuario?idusuario=' . $usuario['idusuario'] . '"><i class="fas fa-times"></i> <span class="hidden-xs">Eliminar</span></div>'
            ];
        }
        return $this->jsonZF($data_out);
    }

    public function agregarUsuarioAction()
    {
        $perfiles = $this->objPerfilesTable->obtenerTodosPerfiles();
        $ferias = $this->objFeriasTable->obtenerFerias($this->sessionContainer['idferias'], $this->sessionContainer['idperfil']);
        $data = [
            'perfiles' => $perfiles,
            'ferias' => $ferias
        ];
        return $this->consoleZF($data);
    }

    public function guardarAgregarUsuarioAction()
    {
        $files = $this->params()->fromFiles('img_perfil');
        $datosFormulario = $this->params()->fromPost();
        $data = [
            'nombres' => $datosFormulario['nombres'],
            'apellido_paterno' => $datosFormulario['apellido_paterno'],
            'apellido_materno' => $datosFormulario['apellido_materno'],
            'email' => $datosFormulario['correo_electronico'],
            'idperfil' => $datosFormulario['idperfil'],
            'idferias' => (isset($datosFormulario['idferias'])) ? $datosFormulario['idferias'] : 0,
            'contrasena' => md5($this->salt . $datosFormulario['contrasena']),
            'usuario' => $datosFormulario['usuario'],
            'telefono' => (int)$datosFormulario['telefono'],
            'idempresas' => (isset($datosFormulario['idempresas'])) ? $datosFormulario['idempresas'] : 0,
            'tipo' => (isset($datosFormulario['tipo'])) ? $datosFormulario['tipo'] : '',
            'encargado' => (isset($datosFormulario['encargado'])) ? $datosFormulario['encargado'] : 0,
        ];

        $token = md5(uniqid());

        if (! empty($files['name'])) {
            if (file_exists(getcwd() . '/public' . $this->dir_avatar . $token . '.jpg')) {
                unlink(getcwd() . '/public' . $this->dir_avatar . $token . '.jpg');
            }
            $this->uploaderService->tojpg = true;
            $this->uploaderService->upload($files['tmp_name'], getcwd() . '/public' . $this->dir_avatar . $token . '.jpg', ['maxwidth' => 50,'maxheight' => 50]);
            $data['token'] = $token;
        }

        $this->objUsuarioTable->agregarUsuario($data);
        return $this->jsonZF(['result' => 'success']);
    }

    public function editarUsuarioAction()
    {
        $idusuario = $this->params()->fromQuery('idusuario');
        $dataUsuario = $this->objUsuarioTable->obternerDatosUsuarios($idusuario);

        if (file_exists(getcwd() . '/public' . $this->dir_avatar . $dataUsuario['token'] . '.jpg')) {
            $dataUsuario['img_user_perfil'] = $this->dir_avatar . $dataUsuario['token'] . '.jpg';
        } else {
            $dataUsuario['img_user_perfil'] = $this->dir_avatar . 'male.png';
        }

        $perfiles = $this->objPerfilesTable->obtenerTodosPerfiles();
        $ferias = $this->objFeriasTable->obtenerFerias($this->sessionContainer['idferias'], $this->sessionContainer['idperfil']);
        $dataUsuario['ferias'] = $ferias;
        $dataUsuario['perfiles'] = $perfiles;
        return $this->consoleZF($dataUsuario);
    }

    public function guardarEditarUsuarioAction()
    {
        $files = $this->params()->fromFiles('img_perfil');
        $idusuario = $this->params()->fromQuery('idusuario');
        $datosFormulario = $this->params()->fromPost();
        $data = [
            'nombres' => $datosFormulario['nombres'],
            'apellido_paterno' => $datosFormulario['apellido_paterno'],
            'apellido_materno' => $datosFormulario['apellido_materno'],
            'idperfil' => $datosFormulario['idperfil'],
            'idferias' => (isset($datosFormulario['idferias'])) ? $datosFormulario['idferias'] : 0,
            'telefono' => (int)$datosFormulario['telefono'],
            'idempresas' => (isset($datosFormulario['idempresas'])) ? $datosFormulario['idempresas'] : 0,
            'tipo' => (isset($datosFormulario['tipo'])) ? $datosFormulario['tipo'] : '',
            'encargado' => (isset($datosFormulario['encargado'])) ? $datosFormulario['encargado'] : 0,
        ];

        $token = md5(uniqid());

        if (! empty($files['name'])) {
            if (file_exists(getcwd() . '/public' . $this->dir_avatar . $token . '.jpg')) {
                unlink(getcwd() . '/public' . $this->dir_avatar . $token . '.jpg');
            }
            $this->uploaderService->tojpg = true;
            $this->uploaderService->upload($files['tmp_name'], getcwd() . '/public' . $this->dir_avatar . $token . '.jpg', ['maxwidth' => 50,'maxheight' => 50]);
            $data['token'] = $token;
        }

        if (! empty($datosFormulario['contrasena'])) {
            $data['contrasena'] = md5($this->salt . $datosFormulario['contrasena']);
        }

        $this->objUsuarioTable->actualizarDatosUsuarios($data, $idusuario);

        return $this->jsonZF(['result' => 'success']);
    }

    public function eliminarUsuarioAction()
    {
        $idusuario = $this->params()->fromQuery('idusuario');
        $dataUsuario = $this->objUsuarioTable->obternerDatosUsuarios($idusuario);
        return $this->consoleZF($dataUsuario);
    }

    public function confirmarEliminarUsuarioAction()
    {
        $idusuario = $this->params()->fromQuery('idusuario');
        $this->objUsuarioTable->eliminarUsuario($idusuario);
        return $this->jsonZF(['result' => 'success']);
    }

    public function activarUsuarioAction()
    {
        $idusuario = $this->params()->fromPost('idusuario');
        $estado = $this->params()->fromPost('estado');
        if ($estado == 'true') {
            $estado = 'A';
        } else {
            $estado = 'D';
        }
        $data = ['estado' => $estado];
        $this->objUsuarioTable->actualizarDatosUsuarios($data, $idusuario);
        return $this->jsonZF(['result' => 'success']);
    }

    public function validarCorreoAction()
    {
        $email = $this->params()->fromQuery('correo_electronico');
        $result = $this->objUsuarioTable->verificarEmail($email);
        if ($result == false) {
            echo '"true"';
        } else {
            echo '"Este correo electrónico ya se encuentra en uso"';
        }
        die;
    }

    public function validarUsuarioAction()
    {
        $usuario = $this->params()->fromQuery('usuario');
        $key = $this->params()->fromQuery('key');
        $result = $this->objUsuarioTable->verificarUsuario($usuario);
        if ($result == false || $usuario == $key) {
            echo '"true"';
        } else {
            echo '"Este usuario ya se encuentra en uso"';
        }
        die;
    }

    public function perfilesAction()
    {
    }

    public function listarPerfilesAction()
    {
        $perfiles = $this->objPerfilesTable->obtenerTodosPerfilesyModulos();
        $data_out = [];
        $data_out['data'] = [];
        $blocked = '';
        foreach ($perfiles as $perfil) {
            $modulos = [];
            foreach ($perfil['modulos'] as $modulo) {
                if (isset($modulo['icono'])) {
                    $modulos[] = '<i class="' . $modulo['icono'] . '"></i> ' . $modulo['nombre'];
                }
            }
            $modulos = implode('<br/>', $modulos);
            $usuarios = [];
            $k = 0;
            foreach ($perfil['usuarios'] as $usuario) {
                $usuarios[] = '<i class="fas fa-user"></i> ' . $usuario['nombre'] . ' ' . $usuario['apellido_paterno'] . ' ' . $usuario['apellido_materno'];
                if ($k > 15) {
                    $usuarios[] = 'Y otros ' . count($perfil['usuarios']) . ' usuarios más.';
                    break;
                }
                $k++;
            }
            if ($perfil['idperfil'] == 1) {
                $blocked = 'disabled';
            } else {
                $blocked = '';
            }
            $usuarios = implode('<br/>', $usuarios);
            $data_out['data'][] = [
                $perfil['nombre'] . '<br/><small class="text-muted">' . $perfil['descripcion'] . '</small>',
                $usuarios,
                $modulos,
                '<div class="clas btn btn-sm btn-info pop-up" href="/sistema/editar-perfil?idperfil=' . $perfil['idperfil'] . '"><i class="fas fa-pencil-alt"></i> <span class="hidden-xs">Editar</span></div> <div class="clas btn btn-sm btn-danger pop-up ' . $blocked . '" href="/sistema/eliminar-perfil?idperfil=' . $perfil['idperfil'] . '"><i class="fas fa-times"></i> <span class="hidden-xs">Eliminar</span></div>'
            ];
        }
        return $this->jsonZF($data_out);
    }

    public function agregarPerfilAction()
    {
        $modulos = $this->objPerfilesTable->obtenerTodosModulos();
        $data_modulos = [];
        foreach ($modulos as $modulo) {
            if (! isset($data_modulos[$modulo['idmodulos']])) {
                $data_modulos[$modulo['idmodulos']] = [
                    'nombre' => $modulo['nombre'],
                    'icono' => $modulo['icono'],
                    'idmodulos' => $modulo['idmodulos'],
                    'submodulos' => []
                ];
            }
            $data_modulos[$modulo['idmodulos']]['submodulos'][] = [
                'nombre' => $modulo['nombre_submodulo'],
                'icono' => $modulo['icono_submodulo'],
                'idsubmodulo' => $modulo['idsubmodulo']
            ];
        }
        return $this->consoleZF(['modulos' => $data_modulos]);
    }

    public function guardarAgregarPerfilAction()
    {
        $dataFormulario = $this->params()->fromPost();
        $data_perfil = [
            'nombre' => $dataFormulario['nombre'],
            'descripcion' => $dataFormulario['descripcion']
        ];
        $data_submodulos = $this->params()->fromPost('submodulos');
        $this->objPerfilesTable->agregarPerfil($data_perfil, $data_submodulos);
        return $this->jsonZF(['result' => 'success']);
    }

    public function editarPerfilAction()
    {
        $idperfil = $this->params()->fromQuery('idperfil');
        $dataPerfil = $this->objPerfilesTable->obtenerPerfilyModulos($idperfil);
        $modulos = $this->objPerfilesTable->obtenerTodosModulos();
        $data_modulos = [];
        foreach ($modulos as $modulo) {
            if (! isset($data_modulos[$modulo['idmodulos']])) {
                $data_modulos[$modulo['idmodulos']] = [
                    'nombre' => $modulo['nombre'],
                    'icono' => $modulo['icono'],
                    'idmodulos' => $modulo['idmodulos'],
                    'submodulos' => []
                ];
            }
            $data_modulos[$modulo['idmodulos']]['submodulos'][] = [
                'nombre' => $modulo['nombre_submodulo'],
                'icono' => $modulo['icono_submodulo'],
                'idsubmodulo' => $modulo['idsubmodulo']
            ];
        }
        $submodulos = [];
        foreach ($dataPerfil['dataModulos'] as $modulo) {
            if (! in_array($modulo['idsubmodulo'], $submodulos)) {
                $submodulos[] = $modulo['idsubmodulo'];
            }
        }
        return $this->consoleZF(['modulos' => $data_modulos,'dataPerfil' => $dataPerfil['dataPerfil'],'submodulos' => $submodulos]);
    }

    public function guardarEditarPerfilAction()
    {
        $idperfil = $this->params()->fromQuery('idperfil');
        $dataFormulario = $this->params()->fromPost();
        $data_perfil = [
            'nombre' => $dataFormulario['nombre'],
            'descripcion' => $dataFormulario['descripcion']
        ];
        $data_submodulos = $this->params()->fromPost('submodulos');
        if (empty($data_submodulos)) {
            $data_submodulos = [];
        }
        $this->objPerfilesTable->editarPerfil($data_perfil, $data_submodulos, $idperfil);
        return $this->jsonZF(['result' => 'success']);
    }

    public function eliminarPerfilAction()
    {
        $idperfil = $this->params()->fromQuery('idperfil');
        $dataPerfil = $this->objPerfilesTable->obtenerDatosPerfil($idperfil);
        return $this->consoleZF($dataPerfil);
    }

    public function confirmarEliminarPerfilAction()
    {
        $idperfil = $this->params()->fromQuery('idperfil');
        $this->objPerfilesTable->eliminarPerfil($idperfil);
        return $this->jsonZF(['result' => 'success']);
    }

    public function modulosAction()
    {
    }

    public function listarModulosAction()
    {
        $dataModulos = $this->objModulosTable->obtenerDatosModulos();
        $data_out = [];
        $data_out['data'] = [];
        foreach ($dataModulos as $item) {
            $dataSubModulos = $this->objSubModulosTable->obtenerDatosSubModulos(['idmodulo' => $item['idmodulos']]);
            $submodulos = '';

            if (! empty($dataSubModulos)) {
                foreach ($dataSubModulos as $submodulo) {
                    $submodulos .= '<div><i class="' . $submodulo['icono'] . '"></i> ' . $submodulo['nombre'] . '</div>';
                }
            }

            $data_out['data'][] = [
                $item['nombre'],
                $submodulos,
                '<div class="clas btn btn-sm btn-info pop-up-2" href="/sistema/editar-modulo?idmodulos=' . $item['idmodulos'] . '"><i class="fas fa-pencil-alt"></i> <span class="hidden-xs">Editar</span></div>'
            ];
        }
        return $this->jsonZF($data_out);
    }

    public function agregarModuloAction()
    {
        $data = [];
        return $this->consoleZF($data);
    }

    public function guardarAgregarModuloAction()
    {
        $datosFormulario = $this->params()->fromPost();
        $data = [
            'nombre' => $datosFormulario['nombre'],
            'ruta' => $datosFormulario['ruta'],
            'icono' => $datosFormulario['icono'],
            'col_grupo_dash' => 1,
            'pos_grupo_dash' => 1
        ];

        $lastIdModulo = $this->objModulosTable->agregarModulo($data);
        $dataSubModulos = ($datosFormulario['submodulos'] != '') ? json_decode($datosFormulario['submodulos'], true) : [];

        if (! empty($dataSubModulos) && $lastIdModulo > 0) {
            foreach ($dataSubModulos as $item) {
                if ($item['nombre'] == '') {
                    continue;
                }
                $data = [
                    'nombre' => $item['nombre'],
                    'ruta' => $item['ruta'],
                    'idmodulo' => $lastIdModulo,
                    'icono' => $item['icono'],
                    'visible_grupo_dash' => 1,
                    'desc_grupo_dash' => $item['descripcion']
                ];
                $this->objSubModulosTable->agregarSubModulo($data);
            }
        }

        return $this->jsonZF(['result' => 'success']);
    }

    public function editarModuloAction()
    {
        $idmodulos = $this->params()->fromQuery('idmodulos');
        $dataModulo = $this->objModulosTable->obtenerDatoModulos($idmodulos);
        $dataModulo['submodulos'] = $this->objSubModulosTable->obtenerDatosSubModulos(['idmodulo' => $dataModulo['idmodulos']]);
        return $this->consoleZF($dataModulo);
    }

    public function guardarEditarModuloAction()
    {
        $idmodulos = $this->params()->fromQuery('idmodulos');
        $datosFormulario = $this->params()->fromPost();
        $data = [
            'nombre' => $datosFormulario['nombre'],
            'ruta' => $datosFormulario['ruta'],
            'icono' => $datosFormulario['icono'],
            'col_grupo_dash' => 1,
            'pos_grupo_dash' => 1
        ];

        $this->objModulosTable->actualizarDatosModulos($data, $idmodulos);
        $dataSubModulos = ($datosFormulario['submodulos'] != '') ? json_decode($datosFormulario['submodulos'], true) : [];

        $idSubModuloSeleccionados = [];

        if (! empty($dataSubModulos) && $idmodulos > 0) {
            foreach ($dataSubModulos as $item) {
                if ($item['nombre'] == '') {
                    continue;
                }

                $data = [
                    'nombre' => $item['nombre'],
                    'ruta' => $item['ruta'],
                    'idmodulo' => $idmodulos,
                    'icono' => $item['icono'],
                    'visible_grupo_dash' => 1,
                    'desc_grupo_dash' => $item['descripcion']
                ];

                if (isset($item['idsubmodulo']) && $item['idsubmodulo'] > 0) {
                    $this->objSubModulosTable->actualizarDatosSubModulos($data, $item['idsubmodulo']);
                } else {
                    $this->objSubModulosTable->agregarSubModulo($data);
                }

                if (isset($item['idsubmodulo'])) {
                    $idSubModuloSeleccionados[] = $item['idsubmodulo'];
                }
            }

            ////////// SCRIPT PARA ELIMINAR LOS SUDMODULOS ELIMINADOS [INICIO] //////////
            $listaidsubmodulos = explode(',', $datosFormulario['listaidsubmodulos']);
            if (! empty($listaidsubmodulos) && $idSubModuloSeleccionados > 0) {
                foreach ($listaidsubmodulos as $idsubmodulo) {
                    if (! in_array($idsubmodulo, $idSubModuloSeleccionados)) {
                        $this->objSubModulosTable->eliminarSubModulo(['idsubmodulo' => $idsubmodulo]);
                    }
                }
            }
            ////////// SCRIPT PARA ELIMINAR LOS SUDMODULOS ELIMINADOS [FIN] //////////
        }

        return $this->jsonZF(['result' => 'success']);
    }

    public function seleccionarIconoAction()
    {
        return $this->consoleZF([]);
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
