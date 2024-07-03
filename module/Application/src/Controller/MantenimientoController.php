<?php

namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\View\Model\JsonModel;

class MantenimientoController extends AbstractActionController
{
    protected $serviceManager;
    protected $objPlanesTable;
    protected $objStandTable;
    protected $objStandModeloTable;
    protected $objPaginasTable;
    protected $objPlanesPaginasTable;
    protected $objMenusTable;
    protected $objPlanesMenusTable;
    protected $objSectoresTable;
    protected $objBancosTable;
    protected $imagenExtensionesValidas;
    protected $objPlataformasTable;
    protected $objTools;

    public function __construct($serviceManager, $objPlanesTable, $objStandTable, $objStandModeloTable, $objPaginasTable, $objPlanesPaginasTable, $objMenusTable, $objPlanesMenusTable, $objSectoresTable, $objBancosTable, $objPlataformasTable, $objTools)
    {
        $this->serviceManager = $serviceManager;
        $this->objPlanesTable = $objPlanesTable;
        $this->objStandTable = $objStandTable;
        $this->objStandModeloTable = $objStandModeloTable;
        $this->objPaginasTable = $objPaginasTable;
        $this->objPlanesPaginasTable = $objPlanesPaginasTable;
        $this->objMenusTable = $objMenusTable;
        $this->objPlanesMenusTable = $objPlanesMenusTable;
        $this->objSectoresTable = $objSectoresTable;
        $this->objBancosTable = $objBancosTable;
        $this->imagenExtensionesValidas = ['jpg','jpeg','png','gif','svg'];
        $this->objPlataformasTable = $objPlataformasTable;
        $this->objTools = $objTools;
    }

    public function planesAction()
    {
    }

    public function listarPlanesAction()
    {
        $dataPlanes = $this->objPlanesTable->obtenerPlanes();
        $data_out = [];
        $data_out['data'] = [];
        foreach ($dataPlanes as $item) {
            $btnOrden = '<div class="opciones-flechas" data-ids="' . $item['idplanes'] . '"><button class="flecha-arriba"><i class="fas fa-sort-up"></i></button><button class="flecha-abajo"><i class="fas fa-sort-down"></i></button></div>';
            $data_out['data'][] = [
                $item['nombre'],
                $item['cantidad_zonas'],
                $item['cantidad_empresas_zonas'],
                '<div class="clas btn btn-sm btn-info pop-up" href="/mantenimiento/editar-planes?idplanes=' . $item['idplanes'] . '"><i class="fas fa-pencil-alt"></i> <span class="hidden-xs">Editar</span></div> <div class="clas btn btn-sm btn-danger pop-up" href="/mantenimiento/eliminar-planes?idplanes=' . $item['idplanes'] . '"><i class="fas fa-times"></i> <span class="hidden-xs">Eliminar</span></div> ' . $btnOrden
            ];
        }
        return $this->jsonZF($data_out);
    }

    public function agregarPlanesAction()
    {
        $data = [
            'paginas' => $this->objPaginasTable->obtenerPaginas(),
            'menus' => $this->objMenusTable->obtenerMenus()
        ];
        return $this->consoleZF($data);
    }

    public function guardarAgregarPlanesAction()
    {
        $datosFormulario = $this->params()->fromPost();
        $data = [
            'nombre' => $datosFormulario['nombre'],
            'cantidad_zonas' => $datosFormulario['cantidad_zonas'],
            'cantidad_empresas_zonas' => $datosFormulario['cantidad_empresas_zonas'],
            'orden' => $this->objPlanesTable->obtenerUltimoOrdenPlanes()
        ];
        $lastIdPlanes = $this->objPlanesTable->agregarPlanes($data);

        $dataIdPaginas = ( isset($datosFormulario['idpaginas']) ) ? $datosFormulario['idpaginas'] : [];

        if (! empty($dataIdPaginas)) {
            foreach ($dataIdPaginas as $idpagina) {
                $data = [
                    'idplanes' => $lastIdPlanes,
                    'idpaginas' => $idpagina
                ];
                $this->objPlanesPaginasTable->agregarPlanesPaginas($data);
            }
        }

        $dataIdMenus = ( isset($datosFormulario['idmenus']) ) ? $datosFormulario['idmenus'] : [];

        if (! empty($dataIdMenus)) {
            foreach ($dataIdMenus as $idmenu) {
                $data = [
                    'idplanes' => $lastIdPlanes,
                    'idmenus' => $idmenu
                ];
                $this->objPlanesMenusTable->agregarPlanesMenus($data);
            }
        }

        return $this->jsonZF(['result' => 'success']);
    }

    public function editarPlanesAction()
    {
        $idplanes = $this->params()->fromQuery('idplanes');
        $dataPlanes = $this->objPlanesTable->obtenerDatoPlanes(['idplanes' => $idplanes]);
        ////////// IDPAGINAS [INICIO] //////////
        $dataPlanes['paginas'] = $this->objPaginasTable->obtenerPaginas();
        $dataPlanesPaginas = $this->objPlanesPaginasTable->obtenerDatosPlanesPaginas(['idplanes' => $idplanes]);
        $selectIdPaginas = [];
        foreach ($dataPlanesPaginas as $item) {
            $selectIdPaginas[] = $item['idpaginas'];
        }
        $dataPlanes['idpaginas'] = $selectIdPaginas;
        ////////// IDPAGINAS [FIN] //////////
        ////////// IDMENUS [INICIO] //////////
        $dataPlanes['menus'] = $this->objMenusTable->obtenerMenus();
        $dataPlanesMenus = $this->objPlanesMenusTable->obtenerDatosPlanesMenus(['idplanes' => $idplanes]);
        $selectIdMenus = [];
        foreach ($dataPlanesMenus as $item) {
            $selectIdMenus[] = $item['idmenus'];
        }
        $dataPlanes['idmenus'] = $selectIdMenus;
        ////////// IDMENUS [FIN] //////////
        return $this->consoleZF($dataPlanes);
    }

    public function guardarEditarPlanesAction()
    {
        $idplanes = $this->params()->fromQuery('idplanes');
        $datosFormulario = $this->params()->fromPost();
        $data = [
            'nombre' => $datosFormulario['nombre'],
            'cantidad_zonas' => $datosFormulario['cantidad_zonas'],
            'cantidad_empresas_zonas' => $datosFormulario['cantidad_empresas_zonas']
        ];
        $this->objPlanesTable->actualizarDatosPlanes($data, $idplanes);
        ////////// PLANES PAGINAS [INICIO] //////////
        $dataIdPaginas = ( isset($datosFormulario['idpaginas']) ) ? $datosFormulario['idpaginas'] : [];
        $this->objPlanesPaginasTable->eliminarPlanesPaginas(["idplanes" => $idplanes]);
        if (! empty($dataIdPaginas)) {
            foreach ($dataIdPaginas as $idpagina) {
                $data = [
                    'idplanes' => $idplanes,
                    'idpaginas' => $idpagina
                ];
                $this->objPlanesPaginasTable->agregarPlanesPaginas($data);
            }
        }
        ////////// PLANES PAGINAS [FIN] //////////
        ////////// PLANES MENUS [INICIO] //////////
        $dataIdMenus = ( isset($datosFormulario['idmenus']) ) ? $datosFormulario['idmenus'] : [];
        $this->objPlanesMenusTable->eliminarPlanesMenus(['idplanes' => $idplanes]);
        if (! empty($dataIdMenus)) {
            foreach ($dataIdMenus as $idmenu) {
                $data = [
                    'idplanes' => $idplanes,
                    'idmenus' => $idmenu
                ];
                $this->objPlanesMenusTable->agregarPlanesMenus($data);
            }
        }
        ////////// PLANES MENUS [FIN] //////////
        return $this->jsonZF(['result' => 'success']);
    }

    public function eliminarPlanesAction()
    {
        $idplanes = $this->params()->fromQuery('idplanes');
        $dataPlanes = $this->objPlanesTable->obtenerDatoPlanes(['idplanes' => $idplanes]);
        return $this->consoleZF($dataPlanes);
    }

    public function confirmarEliminarPlanesAction()
    {
        $idplanes = $this->params()->fromQuery('idplanes');
        $this->objPlanesTable->eliminarPlanes($idplanes);
        $this->objPlanesPaginasTable->eliminarPlanesPaginas(["idplanes" => $idplanes]);
        $this->objPlanesMenusTable->eliminarPlanesMenus(["idplanes" => $idplanes]);
        $this->objPlanesTable->actualizarOrdenPlanes();
        return $this->jsonZF(['result' => 'success']);
    }

    public function guardarOrdenPlanesAction()
    {
        $datosFormulario = $this->params()->fromPost();
        $dataOrden = $datosFormulario['orden'];
        if (! empty($dataOrden)) {
            foreach ($dataOrden as $item) {
                $this->objPlanesTable->actualizarDatosPlanes(['orden' => $item['orden']], $item['ids']);
            }
        }
        return $this->jsonZF(['result' => 'success']);
    }

    public function standAction()
    {
    }

    public function listarStandAction()
    {
        $dataStand = $this->objStandTable->obtenerStand();
        $data_out = [];
        $data_out['data'] = [];
        foreach ($dataStand as $item) {
            $btnGaleria = '<div class="clas btn btn-sm btn-dark pop-up-2" href="/mantenimiento/galeria-stand?idstand=' . $item['idstand'] . '"><i class="fas fa-images"></i> <span class="hidden-xs">Galeria</span></div>';
            $data_out['data'][] = [
                $item['nombre'],
                $btnGaleria
                //$btnGaleria.' <div class="clas btn btn-sm btn-info pop-up" href="/mantenimiento/editar-stand?idstand='.$item['idstand'].'"><i class="fas fa-pencil-alt"></i> <span class="hidden-xs">Editar</span></div> <div class="clas btn btn-sm btn-danger pop-up" href="/mantenimiento/eliminar-stand?idstand='.$item['idstand'].'"><i class="fas fa-times"></i> <span class="hidden-xs">Eliminar</span></div>'
            ];
        }
        return $this->jsonZF($data_out);
    }

    public function agregarStandAction()
    {
        $data = [];
        return $this->consoleZF($data);
    }


    public function guardarAgregarStandAction()
    {
        $datosFormulario = $this->params()->fromPost();
        $data = [
            'nombre' => $datosFormulario['nombre'],
            'hash_url' => $this->objTools->toAscii($datosFormulario['nombre'])
        ];
        $this->objStandTable->agregarStand($data);
        return $this->jsonZF(['result' => 'success']);
    }

    public function editarStandAction()
    {
        $idstand = $this->params()->fromQuery('idstand');
        $dataStand = $this->objStandTable->obtenerDatoStand(['idstand' => $idstand]);
        return $this->consoleZF($dataStand);
    }

    public function guardarEditarStandAction()
    {
        $idstand = $this->params()->fromQuery('idstand');
        $datosFormulario = $this->params()->fromPost();
        $data = [
            'nombre' => $datosFormulario['nombre'],
            'hash_url' => $this->objTools->toAscii($datosFormulario['nombre'])
        ];
        $this->objStandTable->actualizarDatosStand($data, $idstand);
        return $this->jsonZF(['result' => 'success']);
    }

    public function eliminarStandAction()
    {
        $idstand = $this->params()->fromQuery('idstand');
        $dataStand = $this->objStandTable->obtenerDatoStand(['idstand' => $idstand]);
        return $this->consoleZF($dataStand);
    }

    public function confirmarEliminarStandAction()
    {
        $idstand = $this->params()->fromQuery('idstand');
        $this->objStandTable->eliminarStand($idstand);
        return $this->jsonZF(['result' => 'success']);
    }

    public function paginasAction()
    {
    }

    public function listarPaginasAction()
    {
        $dataPaginas = $this->objPaginasTable->obtenerPaginas();
        $data_out = [];
        $data_out['data'] = [];
        foreach ($dataPaginas as $item) {
            $btnOrden = '<div class="opciones-flechas" data-ids="' . $item['idpaginas'] . '"><button class="flecha-arriba"><i class="fas fa-sort-up"></i></button><button class="flecha-abajo"><i class="fas fa-sort-down"></i></button></div>';
            $data_out['data'][] = [
                $item['nombre'],
                '<div class="clas btn btn-sm btn-info pop-up" href="/mantenimiento/editar-paginas?idpaginas=' . $item['idpaginas'] . '"><i class="fas fa-pencil-alt"></i> <span class="hidden-xs">Editar</span></div> <div class="clas btn btn-sm btn-danger pop-up" href="/mantenimiento/eliminar-paginas?idpaginas=' . $item['idpaginas'] . '"><i class="fas fa-times"></i> <span class="hidden-xs">Eliminar</span></div> ' . $btnOrden
            ];
        }
        return $this->jsonZF($data_out);
    }

    public function agregarPaginasAction()
    {
        $data = [];
        return $this->consoleZF($data);
    }

    public function guardarAgregarPaginasAction()
    {
        $datosFormulario = $this->params()->fromPost();
        $data = [
            'nombre' => $datosFormulario['nombre'],
            'hash_url' => $this->objTools->toAscii($datosFormulario['nombre']),
            'orden' => $this->objPaginasTable->obtenerUltimoOrdenPaginas()
        ];
        $this->objPaginasTable->agregarPaginas($data);
        return $this->jsonZF(['result' => 'success']);
    }

    public function editarPaginasAction()
    {
        $idpaginas = $this->params()->fromQuery('idpaginas');
        $dataPaginas = $this->objPaginasTable->obtenerDatoPaginas(['idpaginas' => $idpaginas]);
        return $this->consoleZF($dataPaginas);
    }

    public function guardarEditarPaginasAction()
    {
        $idpaginas = $this->params()->fromQuery('idpaginas');
        $datosFormulario = $this->params()->fromPost();
        $data = [
            'nombre' => $datosFormulario['nombre'],
            'hash_url' => $this->objTools->toAscii($datosFormulario['nombre'])
        ];
        $this->objPaginasTable->actualizarDatosPaginas($data, $idpaginas);
        return $this->jsonZF(['result' => 'success']);
    }

    public function eliminarPaginasAction()
    {
        $idpaginas = $this->params()->fromQuery('idpaginas');
        $dataPaginas = $this->objPaginasTable->obtenerDatoPaginas(['idpaginas' => $idpaginas]);
        return $this->consoleZF($dataPaginas);
    }

    public function confirmarEliminarPaginasAction()
    {
        $idpaginas = $this->params()->fromQuery('idpaginas');
        $this->objPaginasTable->eliminarPaginas($idpaginas);
        $this->objPaginasTable->actualizarOrdenPaginas();
        return $this->jsonZF(['result' => 'success']);
    }

    private function generarHashUrl($string)
    {
        $buscar = ["á","é","í","ó","ú"];
        $reemplazar = ["a","e","i","o","u"];
        return mb_strtolower(str_replace(" ", "-", str_replace($buscar, $reemplazar, $string)));
    }

    public function guardarOrdenPaginasAction()
    {
        $datosFormulario = $this->params()->fromPost();
        $dataOrden = $datosFormulario['orden'];
        if (! empty($dataOrden)) {
            foreach ($dataOrden as $item) {
                $this->objPaginasTable->actualizarDatosPaginas(['orden' => $item['orden']], $item['ids']);
            }
        }
        return $this->jsonZF(['result' => 'success']);
    }

    public function menusAction()
    {
    }

    public function listarMenusAction()
    {
        $dataMenus = $this->objMenusTable->obtenerMenus();
        $data_out = [];
        $data_out['data'] = [];
        foreach ($dataMenus as $item) {
            $tipo = ( $item['tipo'] == 'E' ) ? 'Encabezado (Interacción de la Feria)' : 'Pie de Página (Interacción del Stand)';
            $data_out['data'][] = [
                $tipo,
                $item['nombre'],
                '<div class="clas btn btn-sm btn-info pop-up" href="/mantenimiento/editar-menus?idmenus=' . $item['idmenus'] . '"><i class="fas fa-pencil-alt"></i> <span class="hidden-xs">Editar</span></div> <div class="clas btn btn-sm btn-danger pop-up" href="/mantenimiento/eliminar-menus?idmenus=' . $item['idmenus'] . '"><i class="fas fa-times"></i> <span class="hidden-xs">Eliminar</span></div>'
            ];
        }
        return $this->jsonZF($data_out);
    }

    public function agregarMenusAction()
    {
        $data = [];
        return $this->consoleZF($data);
    }

    public function guardarAgregarMenusAction()
    {
        $datosFormulario = $this->params()->fromPost();
        $data = [
            'tipo' => $datosFormulario['tipo'],
            'nombre' => $datosFormulario['nombre'],
            'hash_url' => $this->generarHashUrl($datosFormulario['hash_url']),
            'posicion' => $datosFormulario['posicion']
        ];
        $this->objMenusTable->agregarMenus($data);
        return $this->jsonZF(['result' => 'success']);
    }

    public function editarMenusAction()
    {
        $idmenus = $this->params()->fromQuery('idmenus');
        $dataMenus = $this->objMenusTable->obtenerDatoMenus(['idmenus' => $idmenus]);
        return $this->consoleZF($dataMenus);
    }

    public function guardarEditarMenusAction()
    {
        $idmenus = $this->params()->fromQuery('idmenus');
        $datosFormulario = $this->params()->fromPost();
        $data = [
            'tipo' => $datosFormulario['tipo'],
            'nombre' => $datosFormulario['nombre'],
            'hash_url' => $this->generarHashUrl($datosFormulario['hash_url']),
            'posicion' => $datosFormulario['posicion']
        ];
        $this->objMenusTable->actualizarDatosMenus($data, $idmenus);
        return $this->jsonZF(['result' => 'success']);
    }

    public function eliminarMenusAction()
    {
        $idmenus = $this->params()->fromQuery('idmenus');
        $dataMenus = $this->objMenusTable->obtenerDatoMenus(['idmenus' => $idmenus]);
        return $this->consoleZF($dataMenus);
    }

    public function confirmarEliminarMenusAction()
    {
        $idmenus = $this->params()->fromQuery('idmenus');
        $this->objMenusTable->eliminarMenus($idmenus);
        return $this->jsonZF(['result' => 'success']);
    }

    public function sectoresAction()
    {
    }

    public function listarSectoresAction()
    {
        $dataSectores = $this->objSectoresTable->obtenerSectores();
        $data_out = [];
        $data_out['data'] = [];
        foreach ($dataSectores as $item) {
            $btnGaleria = '<div class="clas btn btn-sm btn-dark pop-up-2" href="/mantenimiento/galeria-sectores?idsectores=' . $item['idsectores'] . '"><i class="fas fa-images"></i> <span class="hidden-xs">Galeria</span></div>';
            $data_out['data'][] = [
                $item['nombre'],
                $btnGaleria . ' <div class="clas btn btn-sm btn-info pop-up" href="/mantenimiento/editar-sectores?idsectores=' . $item['idsectores'] . '"><i class="fas fa-pencil-alt"></i> <span class="hidden-xs">Editar</span></div> <div class="clas btn btn-sm btn-danger pop-up" href="/mantenimiento/eliminar-sectores?idsectores=' . $item['idsectores'] . '"><i class="fas fa-times"></i> <span class="hidden-xs">Eliminar</span></div>'
            ];
        }
        return $this->jsonZF($data_out);
    }

    public function agregarSectoresAction()
    {
        $data = [];
        return $this->consoleZF($data);
    }

    public function guardarAgregarSectoresAction()
    {
        $datosFormulario = $this->params()->fromPost();
        $data = [
            'nombre' => $datosFormulario['nombre'],
            'hash_url' => $this->objTools->toAscii($datosFormulario['nombre'])
        ];
        $this->objSectoresTable->agregarSectores($data);
        return $this->jsonZF(['result' => 'success']);
    }

    public function editarSectoresAction()
    {
        $idsectores = $this->params()->fromQuery('idsectores');
        $dataSectores = $this->objSectoresTable->obtenerDatoSectores(['idsectores' => $idsectores]);
        return $this->consoleZF($dataSectores);
    }

    public function guardarEditarSectoresAction()
    {
        $idsectores = $this->params()->fromQuery('idsectores');
        $datosFormulario = $this->params()->fromPost();
        $data = [
            'nombre' => $datosFormulario['nombre'],
            'hash_url' => $this->objTools->toAscii($datosFormulario['nombre'])
        ];
        $this->objSectoresTable->actualizarDatosSectores($data, $idsectores);
        return $this->jsonZF(['result' => 'success']);
    }

    public function eliminarSectoresAction()
    {
        $idsectores = $this->params()->fromQuery('idsectores');
        $dataSectores = $this->objSectoresTable->obtenerDatoSectores(['idsectores' => $idsectores]);
        return $this->consoleZF($dataSectores);
    }

    public function confirmarEliminarSectoresAction()
    {
        $idsectores = $this->params()->fromQuery('idsectores');
        $this->objSectoresTable->eliminarSectores($idsectores);
        return $this->jsonZF(['result' => 'success']);
    }

    public function galeriaSectoresAction()
    {
        $idsectores = $this->params()->fromQuery('idsectores');
        $dataSectores = $this->objSectoresTable->obtenerDatoSectores(['idsectores' => $idsectores]);
        return $this->consoleZF($dataSectores);
    }

    public function galeriaStandAction()
    {
        $idstand = $this->params()->fromQuery('idstand');
        $dataStand = $this->objStandTable->obtenerDatoStand(['idstand' => $idstand]);
        return $this->consoleZF($dataStand);
    }

    public function bancosAction()
    {
    }

    public function listarBancosAction()
    {
        $dataBancos = $this->objBancosTable->obtenerBancos();
        $data_out = [];
        $data_out['data'] = [];
        foreach ($dataBancos as $item) {
            $data_out['data'][] = [
                $item['nombre'],
                '<div class="clas btn btn-sm btn-info pop-up" href="/mantenimiento/editar-bancos?idbancos=' . $item['idbancos'] . '"><i class="fas fa-pencil-alt"></i> <span class="hidden-xs">Editar</span></div> <div class="clas btn btn-sm btn-danger pop-up" href="/mantenimiento/eliminar-bancos?idbancos=' . $item['idbancos'] . '"><i class="fas fa-times"></i> <span class="hidden-xs">Eliminar</span></div>'
            ];
        }
        return $this->jsonZF($data_out);
    }

    public function agregarBancosAction()
    {
        $data = [];
        return $this->consoleZF($data);
    }

    public function guardarAgregarBancosAction()
    {
        $imagenLogo = $this->params()->fromFiles('logo');
        $imagenLogoSmall = $this->params()->fromFiles('logo_small');
        $imagenLogoModulo = $this->params()->fromFiles('logo_modulo');
        $datosFormulario = $this->params()->fromPost();
        $data = [
            'nombre' => $datosFormulario['nombre'],
            'correo' => $datosFormulario['correo'],
            'telefono' => $datosFormulario['telefono'],
            'enlace' => $datosFormulario['enlace'],
            'enlace_wsp' => $datosFormulario['enlace_wsp'],
        ];
        $directorioLogo = getcwd() . '/public/bancos/logo';
        ////////// SCRIPT PARA GUARDAR IMAGEN [INICIO] //////////
        $datosImagenLogo = [];
        $datosImagenLogo['id'] = md5(uniqid());
        if ($imagenLogo['size'] !== 0) {
            $datosImagenLogo['extension'] = strtolower(pathinfo($imagenLogo['name'])['extension']);
            if (in_array($datosImagenLogo['extension'], $this->imagenExtensionesValidas)) {
                $datosImagenLogo['nombre_completo'] = $datosImagenLogo['id'] . '.' . $datosImagenLogo['extension'];
                $datosImagenLogo['nombre_original'] = $imagenLogo['name'];
                if (move_uploaded_file($imagenLogo['tmp_name'], $directorioLogo . '/' . $datosImagenLogo['nombre_completo'])) {
                    $data['hash_logo'] = $datosImagenLogo['nombre_completo'];
                    $data['nombre_logo'] = $datosImagenLogo['nombre_original'];
                }
            }
        }
        ////////// SCRIPT PARA GUARDAR IMAGEN [FIN] //////////
        $datosImagenLogo = [];
        $datosImagenLogo['id'] = md5(uniqid());
        if ($imagenLogoSmall['size'] !== 0) {
            $datosImagenLogo['extension'] = strtolower(pathinfo($imagenLogoSmall['name'])['extension']);
            if (in_array($datosImagenLogo['extension'], $this->imagenExtensionesValidas)) {
                $datosImagenLogo['nombre_completo'] = $datosImagenLogo['id'] . '.' . $datosImagenLogo['extension'];
                $datosImagenLogo['nombre_original'] = $imagenLogoSmall['name'];
                if (move_uploaded_file($imagenLogoSmall['tmp_name'], $directorioLogo . '/' . $datosImagenLogo['nombre_completo'])) {
                    $data['hash_logo_small'] = $datosImagenLogo['nombre_completo'];
                    $data['nombre_logo_small'] = $datosImagenLogo['nombre_original'];
                }
            }
        }
        ////////// SCRIPT PARA GUARDAR IMAGEN [FIN] //////////
        $datosImagenLogo = [];
        $datosImagenLogo['id'] = md5(uniqid());
        if ($imagenLogoModulo['size'] !== 0) {
            $datosImagenLogo['extension'] = strtolower(pathinfo($imagenLogoModulo['name'])['extension']);
            if (in_array($datosImagenLogo['extension'], $this->imagenExtensionesValidas)) {
                $datosImagenLogo['nombre_completo'] = $datosImagenLogo['id'] . '.' . $datosImagenLogo['extension'];
                $datosImagenLogo['nombre_original'] = $imagenLogoModulo['name'];
                if (move_uploaded_file($imagenLogoModulo['tmp_name'], $directorioLogo . '/' . $datosImagenLogo['nombre_completo'])) {
                    $data['hash_logo_modulo'] = $datosImagenLogo['nombre_completo'];
                    $data['nombre_logo_modulo'] = $datosImagenLogo['nombre_original'];
                }
            }
        }
        ////////// SCRIPT PARA GUARDAR IMAGEN [FIN] //////////
        $this->objBancosTable->agregarBancos($data);
        return $this->jsonZF(['result' => 'success']);
    }

    public function editarBancosAction()
    {
        $idbancos = $this->params()->fromQuery('idbancos');
        $dataBancos = $this->objBancosTable->obtenerDatoBancos(['idbancos' => $idbancos]);
        return $this->consoleZF($dataBancos);
    }

    public function guardarEditarBancosAction()
    {
        $imagenLogo = $this->params()->fromFiles('logo');
        $imagenLogoSmall = $this->params()->fromFiles('logo_small');
        $imagenLogoModulo = $this->params()->fromFiles('logo_modulo');
        $idbancos = $this->params()->fromQuery('idbancos');
        $datosFormulario = $this->params()->fromPost();
        $data = [
            'nombre' => $datosFormulario['nombre'],
            'correo' => $datosFormulario['correo'],
            'telefono' => $datosFormulario['telefono'],
            'enlace' => $datosFormulario['enlace'],
            'enlace_wsp' => $datosFormulario['enlace_wsp'],
        ];
        $directorioLogo = getcwd() . '/public/bancos/logo';
        $dataBancos = $this->objBancosTable->obtenerDatoBancos(['idbancos' => $idbancos]);
        ////////// SCRIPT PARA ACTUALIZAR IMAGEN [INICIO] //////////
        $datosImagenLogo = [];
        $datosImagenLogo['id'] = md5(uniqid());
        if (! empty($imagenLogo['name']) && $imagenLogo['size'] !== 0) {
            if ($dataBancos) {
                if (file_exists($directorioLogo . '/' . $dataBancos['hash_logo'])) {
                    @unlink($directorioLogo . '/' . $dataBancos['hash_logo']);
                }
                $datosImagenLogo['extension'] = strtolower(pathinfo($imagenLogo['name'])['extension']);
                if (in_array($datosImagenLogo['extension'], $this->imagenExtensionesValidas)) {
                    $datosImagenLogo['nombre_completo'] = $datosImagenLogo['id'] . '.' . $datosImagenLogo['extension'];
                    $datosImagenLogo['nombre_original'] = $imagenLogo['name'];
                    if (move_uploaded_file($imagenLogo['tmp_name'], $directorioLogo . '/' . $datosImagenLogo['nombre_completo'])) {
                        $data['hash_logo'] = $datosImagenLogo['nombre_completo'];
                        $data['nombre_logo'] = $datosImagenLogo['nombre_original'];
                    }
                }
            }
        }
        ////////// SCRIPT PARA ACTUALIZAR IMAGEN [FIN] //////////
        $datosImagenLogo = [];
        $datosImagenLogo['id'] = md5(uniqid());
        if (! empty($imagenLogoSmall['name']) && $imagenLogoSmall['size'] !== 0) {
            if ($dataBancos) {
                if (file_exists($directorioLogo . '/' . $dataBancos['hash_logo_small'])) {
                    @unlink($directorioLogo . '/' . $dataBancos['hash_logo_small']);
                }
                $datosImagenLogo['extension'] = strtolower(pathinfo($imagenLogoSmall['name'])['extension']);
                if (in_array($datosImagenLogo['extension'], $this->imagenExtensionesValidas)) {
                    $datosImagenLogo['nombre_completo'] = $datosImagenLogo['id'] . '.' . $datosImagenLogo['extension'];
                    $datosImagenLogo['nombre_original'] = $imagenLogoSmall['name'];
                    if (move_uploaded_file($imagenLogoSmall['tmp_name'], $directorioLogo . '/' . $datosImagenLogo['nombre_completo'])) {
                        $data['hash_logo_small'] = $datosImagenLogo['nombre_completo'];
                        $data['nombre_logo_small'] = $datosImagenLogo['nombre_original'];
                    }
                }
            }
        }
        ////////// SCRIPT PARA ACTUALIZAR IMAGEN [FIN] //////////
        $datosImagenLogo = [];
        $datosImagenLogo['id'] = md5(uniqid());
        if (! empty($imagenLogoModulo['name']) && $imagenLogoModulo['size'] !== 0) {
            if ($dataBancos) {
                if (file_exists($directorioLogo . '/' . $dataBancos['hash_logo_modulo'])) {
                    @unlink($directorioLogo . '/' . $dataBancos['hash_logo_modulo']);
                }
                $datosImagenLogo['extension'] = strtolower(pathinfo($imagenLogoModulo['name'])['extension']);
                if (in_array($datosImagenLogo['extension'], $this->imagenExtensionesValidas)) {
                    $datosImagenLogo['nombre_completo'] = $datosImagenLogo['id'] . '.' . $datosImagenLogo['extension'];
                    $datosImagenLogo['nombre_original'] = $imagenLogoModulo['name'];
                    if (move_uploaded_file($imagenLogoModulo['tmp_name'], $directorioLogo . '/' . $datosImagenLogo['nombre_completo'])) {
                        $data['hash_logo_modulo'] = $datosImagenLogo['nombre_completo'];
                        $data['nombre_logo_modulo'] = $datosImagenLogo['nombre_original'];
                    }
                }
            }
        }
        ////////// SCRIPT PARA ACTUALIZAR IMAGEN [FIN] //////////
        $this->objBancosTable->actualizarDatosBancos($data, $idbancos);
        return $this->jsonZF(['result' => 'success']);
    }

    public function eliminarBancosAction()
    {
        $idbancos = $this->params()->fromQuery('idbancos');
        $dataBancos = $this->objBancosTable->obtenerDatoBancos(['idbancos' => $idbancos]);
        return $this->consoleZF($dataBancos);
    }

    public function confirmarEliminarBancosAction()
    {
        $idbancos = $this->params()->fromQuery('idbancos');
        $dataBancos = $this->objBancosTable->obtenerDatoBancos(['idbancos' => $idbancos]);
        $directorioLogo = getcwd() . '/public/bancos/logo';
        if ($dataBancos) {
            if (file_exists($directorioLogo . '/' . $dataBancos['hash_logo'])) {
                @unlink($directorioLogo . '/' . $dataBancos['hash_logo']);
            }
            if (file_exists($directorioLogo . '/' . $dataBancos['hash_logo_small'])) {
                @unlink($directorioLogo . '/' . $dataBancos['hash_logo_small']);
            }
            if (file_exists($directorioLogo . '/' . $dataBancos['hash_logo_modulo'])) {
                @unlink($directorioLogo . '/' . $dataBancos['hash_logo_modulo']);
            }
        }
        $this->objBancosTable->eliminarBancos($idbancos);
        return $this->jsonZF(['result' => 'success']);
    }

    public function plataformasAction()
    {
    }

    public function listarPlataformasAction()
    {
        $dataPlataformas = $this->objPlataformasTable->obtenerPlataformas();
        $data_out = [];
        $data_out['data'] = [];
        foreach ($dataPlataformas as $item) {
            $data_out['data'][] = [
                $item['nombre'],
                '<div class="clas btn btn-sm btn-info pop-up" href="/mantenimiento/editar-plataformas?idplataformas=' . $item['idplataformas'] . '"><i class="fas fa-pencil-alt"></i> <span class="hidden-xs">Editar</span></div> <div class="clas btn btn-sm btn-danger pop-up disabled" href="/mantenimiento/eliminar-plataformas?idplataformas=' . $item['idplataformas'] . '"><i class="fas fa-times"></i> <span class="hidden-xs">Eliminar</span></div>'
            ];
        }
        return $this->jsonZF($data_out);
    }

    public function agregarPlataformasAction()
    {
        $data = [];
        return $this->consoleZF($data);
    }

    public function guardarAgregarPlataformasAction()
    {
        $datosFormulario = $this->params()->fromPost();
        $data = [
            'nombre' => $datosFormulario['nombre']
        ];
        $this->objPlataformasTable->agregarPlataformas($data);
        return $this->jsonZF(['result' => 'success']);
    }

    public function editarPlataformasAction()
    {
        $idplataformas = $this->params()->fromQuery('idplataformas');
        $dataPlataformas = $this->objPlataformasTable->obtenerDatoPlataformas(['idplataformas' => $idplataformas]);
        return $this->consoleZF($dataPlataformas);
    }

    public function guardarEditarPlataformasAction()
    {
        $idplataformas = $this->params()->fromQuery('idplataformas');
        $datosFormulario = $this->params()->fromPost();
        $data = [
            'nombre' => $datosFormulario['nombre']
        ];
        $this->objPlataformasTable->actualizarDatosPlataformas($data, $idplataformas);
        return $this->jsonZF(['result' => 'success']);
    }

    public function eliminarPlataformasAction()
    {
        $idplataformas = $this->params()->fromQuery('idplataformas');
        $dataPlataformas = $this->objPlataformasTable->obtenerDatoPlataformas(['idplataformas' => $idplataformas]);
        return $this->consoleZF($dataPlataformas);
    }

    public function confirmarEliminarPlataformasAction()
    {
        $idplataformas = $this->params()->fromQuery('idplataformas');
        $this->objPlataformasTable->eliminarPlataformas($idplataformas);
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
