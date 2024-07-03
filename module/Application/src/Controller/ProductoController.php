<?php

namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\View\Model\JsonModel;

class ProductoController extends AbstractActionController
{
    protected $serviceManager;
    protected $objProductosTable;
    protected $objEmpresasTable;
    protected $sessionContainer;
    protected $imagenExtensionesValidas;
    protected $objProductosImagenesTable;

    public function __construct($serviceManager, $objProductosTable, $objEmpresasTable, $objProductosImagenesTable)
    {
        $this->serviceManager = $serviceManager;
        $this->objProductosTable = $objProductosTable;
        $this->objEmpresasTable = $objEmpresasTable;
        $this->sessionContainer = $this->serviceManager->get('DatosSession')->datosUsuario;
        $this->imagenExtensionesValidas = ['jpg','jpeg','png'];
        $this->objProductosImagenesTable = $objProductosImagenesTable;
    }

    public function productosAction()
    {
    }

    public function listarProductosAction()
    {
        $dataProductos = $this->objProductosTable->obtenerProductos($this->sessionContainer['idferias'], $this->sessionContainer['idperfil'], $this->sessionContainer['idusuario'], $this->sessionContainer['encargado']);
        $data_out = [];
        $data_out['data'] = [];
        foreach ($dataProductos as $item) {
            $data_out['data'][] = [
                $item['empresa'],
                $item['nombre'],
                $item['enlace'],
                $item['tipo_enlace'],
                $item['usuario'],
                '<div class="clas btn btn-sm btn-info pop-up-2" href="/producto/editar-productos?idproductos=' . $item['idproductos'] . '"><i class="fas fa-pencil-alt"></i> <span class="hidden-xs">Editar</span></div> <div class="clas btn btn-sm btn-danger pop-up-2" href="/producto/eliminar-productos?idproductos=' . $item['idproductos'] . '"><i class="fas fa-times"></i> <span class="hidden-xs">Eliminar</span></div>'
            ];
        }
        return $this->jsonZF($data_out);
    }

    public function agregarProductosAction()
    {
        $data = [
            'empresas' => $this->objEmpresasTable->obtenerEmpresas($this->sessionContainer['idferias'], $this->sessionContainer['idperfil'], $this->sessionContainer['idempresas'])
        ];
        return $this->consoleZF($data);
    }

    public function guardarAgregarProductosAction()
    {
        $archivos = $this->params()->fromFiles();
        $datosFormulario = $this->params()->fromPost();
        $precio_desde = ($datosFormulario['precio_desde'] != '') ? $datosFormulario['precio_desde'] : 0;
        $tc_referencial = ($datosFormulario['tc_referencial'] != '') ? $datosFormulario['tc_referencial'] : 0;
        $data = [
            'nombre' => $datosFormulario['nombre'],
            'enlace' => $datosFormulario['enlace'],
            'tipo_enlace' => @$datosFormulario['tipo_enlace'],
            'idempresas' => $datosFormulario['idempresas'],
            'enlace_wsp' => $datosFormulario['enlace_wsp'],
            'descripcion' => $datosFormulario['descripcion'],
            'direccion' => $datosFormulario['direccion'],
            'informacion' => $datosFormulario['informacion'],
            'enlace_mapa' => $datosFormulario['enlace_mapa'],
            'enlace_recorrido_virtual' => $datosFormulario['enlace_recorrido_virtual'],
            'precio_desde' => $precio_desde,
            'tc_referencial' => $tc_referencial,
            'moneda' => $datosFormulario['moneda'],
            'idusuario' => $this->sessionContainer['idusuario'],
            'institucion_nombre' => $datosFormulario['institucion_nombre'],
            'maestrias' => $datosFormulario['maestrias'],
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
                        $carpetaProductoArchivos = getcwd() . '/public/productos/documentos';
                        $keyDataArchivo[$key] = ['hash' => 'hash_pdf', 'nombre' => 'nombre_pdf'];
                        if ($archivo['size'] > 0 && $archivo['size'] > 5000000) {
                            return $this->jsonZF(['result' => 'file_max_size']);
                        }
                        break;
                    case 'imagen':
                        $imagenExtensionesValidas = $this->imagenExtensionesValidas;
                        $carpetaProductoArchivos = getcwd() . '/public/productos/imagen';
                        $keyDataArchivo[$key] = ['hash' => 'hash_imagen', 'nombre' => 'nombre_imagen'];
                        break;
                    case 'hash_institucion_imagen':
                        $imagenExtensionesValidas = $this->imagenExtensionesValidas;
                        $carpetaProductoArchivos = getcwd() . '/public/productos/institucion/imagen';
                        $keyDataArchivo[$key] = ['hash' => 'hash_institucion_imagen', 'nombre' => 'nombre_institucion_imagen'];
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
        $lastIdProductos = $this->objProductosTable->agregarProductos($data);

        $i = 0;
        if (! empty($archivos) && isset($archivos['imagenes'])) {
            foreach ($archivos['imagenes'] as $key => $archivo) {
                $directorioImagen = getcwd() . '/public/productos/slider';
                $datosImagen = [];
                $datosImagen['id'] = md5(uniqid());
                if ($archivo['size'] !== 0) {
                    $datosImagen['extension'] = strtolower(pathinfo($archivo['name'])['extension']);
                    if (in_array($datosImagen['extension'], $this->imagenExtensionesValidas)) {
                        $datosImagen['nombre_completo'] = $datosImagen['id'] . '.' . $datosImagen['extension'];
                        $datosImagen['nombre_original'] = $archivo['name'];
                        if (move_uploaded_file($archivo['tmp_name'], $directorioImagen . '/' . $datosImagen['nombre_completo'])) {
                            $data = [
                                'idproductos' => $lastIdProductos,
                                'titulo' => $datosFormulario['titulo'][$i],
                                'hash_imagen' => $datosImagen['nombre_completo'],
                                'nombre_imagen' => $datosImagen['nombre_original']
                            ];
                            $this->objProductosImagenesTable->agregarProductosImagenes($data);
                        }
                    }
                }
                $i++;
            }
        }

        return $this->jsonZF(['result' => 'success']);
    }

    public function editarProductosAction()
    {
        $idproductos = $this->params()->fromQuery('idproductos');
        $dataProductos = $this->objProductosTable->obtenerDatoProductos(['idproductos' => $idproductos]);
        $dataProductos['empresas'] = $this->objEmpresasTable->obtenerEmpresas($this->sessionContainer['idferias'], $this->sessionContainer['idperfil'], $this->sessionContainer['idempresas']);
        $dataProductos['imagenes'] = $this->objProductosImagenesTable->obtenerDatosProductosImagenes(['idproductos' => $idproductos]);
        return $this->consoleZF($dataProductos);
    }

    public function guardarEditarProductosAction()
    {
        $archivos = $this->params()->fromFiles();
        $idproductos = $this->params()->fromQuery('idproductos');
        $datosFormulario = $this->params()->fromPost();
        $precio_desde = ($datosFormulario['precio_desde'] != '') ? $datosFormulario['precio_desde'] : 0;
        $tc_referencial = ($datosFormulario['tc_referencial'] != '') ? $datosFormulario['tc_referencial'] : 0;
        $data = [
            'nombre' => $datosFormulario['nombre'],
            'enlace' => $datosFormulario['enlace'],
            'tipo_enlace' => @$datosFormulario['tipo_enlace'],
            'idempresas' => $datosFormulario['idempresas'],
            'enlace_wsp' => $datosFormulario['enlace_wsp'],
            'descripcion' => $datosFormulario['descripcion'],
            'direccion' => $datosFormulario['direccion'],
            'informacion' => $datosFormulario['informacion'],
            'enlace_mapa' => $datosFormulario['enlace_mapa'],
            'enlace_recorrido_virtual' => $datosFormulario['enlace_recorrido_virtual'],
            'precio_desde' => $precio_desde,
            'tc_referencial' => $tc_referencial,
            'moneda' => $datosFormulario['moneda'],
            'idusuario' => $this->sessionContainer['idusuario'],
            'institucion_nombre' => $datosFormulario['institucion_nombre'],
            'maestrias' => $datosFormulario['maestrias'],
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
                        $carpetaProductoArchivos = getcwd() . '/public/productos/documentos';
                        $keyDataArchivo[$key] = ['hash' => 'hash_pdf', 'nombre' => 'nombre_pdf'];
                        if ($archivo['size'] > 0 && $archivo['size'] > 5000000) {
                            return $this->jsonZF(['result' => 'file_max_size']);
                        }
                        break;
                    case 'imagen':
                        $imagenExtensionesValidas = $this->imagenExtensionesValidas;
                        $carpetaProductoArchivos = getcwd() . '/public/productos/imagen';
                        $keyDataArchivo[$key] = ['hash' => 'hash_imagen', 'nombre' => 'nombre_imagen'];
                        break;
                    case 'hash_institucion_imagen':
                        $imagenExtensionesValidas = $this->imagenExtensionesValidas;
                        $carpetaProductoArchivos = getcwd() . '/public/productos/institucion/imagen';
                        $keyDataArchivo[$key] = ['hash' => 'hash_institucion_imagen', 'nombre' => 'nombre_institucion_imagen'];
                        break;
                }
                $datosProductoArchivo['id'] = md5(uniqid());
                if (! empty($archivo['name']) && $archivo['size'] !== 0) {
                    $dataProducto = $this->objProductosTable->obtenerDatoProductos(['idproductos' => $idproductos]);
                    if ($dataProducto) {
                        if (file_exists($carpetaProductoArchivos . '/' . $dataProducto[$keyDataArchivo[$key]['hash']])) {
                            @unlink($carpetaProductoArchivos . '/' . $dataProducto[$keyDataArchivo[$key]['hash']]);
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
        $this->objProductosTable->actualizarDatosProductos($data, $idproductos);

        $listIdsProductosImagenes = [];
        $i = 0;
        if (! empty($archivos) && isset($archivos['imagenes'])) {
            foreach ($archivos['imagenes'] as $key => $archivo) {
                if ($datosFormulario['idproductosimagenes_active'][$i] === 'on_delete') {
                    $listIdsProductosImagenes[] = $datosFormulario['idproductosimagenes'][$i];
                }
                $directorioImagen = getcwd() . '/public/productos/slider';
                $datosImagen = [];
                $datosImagen['id'] = md5(uniqid());
                $dataImagenes = [];
                $dataImagenes['titulo'] = $datosFormulario['titulo'][$i];
                if (! empty($archivo['name']) && $archivo['size'] !== 0) {
                    if ($datosFormulario['idproductosimagenes'][$i] != '') {
                        $dataProductosImagenes = $this->objProductosImagenesTable->obtenerDatoProductosImagenes(['idproductosimagenes' => $datosFormulario['idproductosimagenes'][$i]]);
                        if ($dataProductosImagenes) {
                            if (file_exists($directorioImagen . '/' . $dataProductosImagenes['hash_imagen'])) {
                                @unlink($directorioImagen . '/' . $dataProductosImagenes['hash_imagen']);
                            }
                        }
                    }
                    $datosImagen['extension'] = strtolower(pathinfo($archivo['name'])['extension']);
                    if (in_array($datosImagen['extension'], $this->imagenExtensionesValidas)) {
                        $datosImagen['nombre_completo'] = $datosImagen['id'] . '.' . $datosImagen['extension'];
                        $datosImagen['nombre_original'] = $archivo['name'];
                        if (move_uploaded_file($archivo['tmp_name'], $directorioImagen . '/' . $datosImagen['nombre_completo'])) {
                            $dataImagenes['idproductos'] = $idproductos;
                            $dataImagenes['hash_imagen'] = $datosImagen['nombre_completo'];
                            $dataImagenes['nombre_imagen'] = $datosImagen['nombre_original'];
                            if ($datosFormulario['idproductosimagenes'][$i] != '') {
                                $this->objProductosImagenesTable->actualizarDatosProductosImagenes($dataImagenes, $datosFormulario['idproductosimagenes'][$i]);
                            } else {
                                $this->objProductosImagenesTable->agregarProductosImagenes($dataImagenes);
                            }
                        }
                    }
                } else {
                    if ($datosFormulario['idproductosimagenes'][$i] != '') {
                        $this->objProductosImagenesTable->actualizarDatosProductosImagenes($dataImagenes, $datosFormulario['idproductosimagenes'][$i]);
                    } elseif ($datosFormulario['idproductosimagenes'][$i] == '' && $datosFormulario['titulo'][$i] != '') {
                        $dataImagenes['idproductos'] = $idproductos;
                        $this->objProductosImagenesTable->agregarProductosImagenes($dataImagenes);
                    }
                }
                $i++;
            }
        }

        $this->objProductosImagenesTable->eliminarImagenesOcultos($listIdsProductosImagenes);

        return $this->jsonZF(['result' => 'success']);
    }

    public function eliminarProductosAction()
    {
        $idproductos = $this->params()->fromQuery('idproductos');
        $dataProductos = $this->objProductosTable->obtenerDatoProductos(['idproductos' => $idproductos]);
        return $this->consoleZF($dataProductos);
    }

    public function confirmarEliminarProductosAction()
    {
        $idproductos = $this->params()->fromQuery('idproductos');
        $dataProducto = $this->objProductosTable->obtenerDatoProductos(['idproductos' => $idproductos]);
        if ($dataProducto) {
            $dataArchivos = [
                'pdf' => ['directorio' => getcwd() . '/public/productos/documentos'],
                'imagen' => ['directorio' => getcwd() . '/public/productos/imagen'],
                'institucion_imagen' => ['directorio' => getcwd() . '/public/productos/institucion/imagen']
            ];
            foreach ($dataArchivos as $key => $item) {
                if (isset($dataProducto['hash_' . $key]) && file_exists($item['directorio'] . '/' . $dataProducto['hash_' . $key])) {
                    @unlink($item['directorio'] . '/' . $dataProducto['hash_' . $key]);
                }
            }
            //ELIMINAR TODAS LAS IMAGENES ASOCIADOS AL PRODUCTO
            $dataProductosImagenes = $this->objProductosImagenesTable->obtenerDatosProductosImagenes(['idproductos' => $idproductos]);
            $listIdsProductosImagenes = [];
            if (! empty($dataProductosImagenes)) {
                foreach ($dataProductosImagenes as $item) {
                    $listIdsProductosImagenes[] = $item['idproductosimagenes'];
                }
            }
            $this->objProductosImagenesTable->eliminarImagenesOcultos($listIdsProductosImagenes);
        }
        $this->objProductosTable->eliminarProductos($idproductos);
        return $this->jsonZF(['result' => 'success']);
    }

    public function importarProductosAction()
    {
        $data = [
            'empresas' => $this->objEmpresasTable->obtenerEmpresas($this->sessionContainer['idferias'], $this->sessionContainer['idperfil'], $this->sessionContainer['idempresas'])
        ];
        return $this->consoleZF($data);
    }

    public function guardarImportarProductosAction()
    {
        $datosFormulario = $this->params()->fromPost();
        $archivoProducto = $this->params()->fromFiles('archivo');
        $carpetaTemporal = getcwd() . '/public/tmp';
        $archivo = [];

        if ($archivoProducto['size'] !== 0) {
            $extensionArchivo = strtolower(pathinfo($archivoProducto['name'])['extension']);

            if ($extensionArchivo === 'xlsx') {
                $archivo['extension'] = $extensionArchivo;
                $archivo['nombre'] = $this->sessionContainer['idusuario'] . '.' . $archivo['extension'];

                if (move_uploaded_file($archivoProducto['tmp_name'], $carpetaTemporal . '/' . $archivo['nombre'])) {
                    if (file_exists($carpetaTemporal . '/' . $archivo['nombre'])) {
                        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($carpetaTemporal . '/' . $archivo['nombre']);
                        $xls_data = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

                        if ($xls_data[1]['A'] === 'Nombre') {
                            unset($xls_data[1]);

                            $dataProductosExistentes = [];

                            foreach ($xls_data as $row) {
                                $nombre = trim($row['A']);

                                if ($nombre == '') {
                                    continue;
                                }

                                $data = [
                                    'idempresas' => $datosFormulario['idempresas'],
                                    'nombre' => $this->reemplazarCaracteresRaros($nombre),
                                    'idusuario' => $this->sessionContainer['idusuario'],
                                ];

                                $dataProducto = $this->objProductosTable->obtenerDatoProductos(['idempresas' => $datosFormulario['idempresas'], 'nombre' => $nombre]);

                                if (! $dataProducto) {
                                    $this->objProductosTable->agregarProductos($data);
                                } else {
                                    $dataProductosExistentes[] = $data;
                                }
                            }
                        } else {
                            return $this->jsonZF(['result' => 'formato_incorrecto']);
                        }

                        if (! empty($dataProductosExistentes)) {
                            return $this->jsonZF(['result' => 'visitantes_existentes', 'data' => $dataProductosExistentes, 'total_visitantes_existentes' => count($dataProductosExistentes)]);
                        } else {
                            return $this->jsonZF(['result' => 'success']);
                        }
                    }
                }
            } else {
                return $this->jsonZF(['result' => 'extension_invalida']);
            }
        }

        return $this->jsonZF(['result' => 'error']);
    }

    private function reemplazarCaracteresRaros($string = null)
    {
        $buscar = ['Ή','Ί','Ό','Ύ','Ώ','ΐ','α','β','γ','ι','λ','κ','μ','ξ','π','ρ','σ','τ','φ','χ','ψ','ω','Ϊ','Ϋ','ά','έ','ή','ί','ΰ','α','β','γ','δ','ε','ζ','η','θ','ι','κ','λ','μ','ξ','π','ρ','ς','σ','τ','φ','χ','ψ','ω','ϊ','ϋ','ό','ύ','ώ','Ё','Ђ','Ѓ','Є','Ѕ','І','Ї','Ј','Љ','Њ','Ћ','Ќ','Ў','Џ','А','Б','В','Г','Д','Е','Ж','И','Й','К','Л','М','Н','О','П','Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ъ','Ы','Ь','Э','Ю','Я','а','б','в','г','д','е','ж','з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','я','ё','ђ','ѓ','є','ѕ','і','ј','љ','њ','ћ','ќ','ў','џ','Ґ','ґ','Ẁ','ẁ','Ẃ','ẃ','ẅ','Ẅ','Ỳ','ỳ','–','—','―','‗','†','†','…','‰','‹','›','‼','‾','⁄','ⁿ','₣','₤','₧','€','℅','ℓ','№','™','Ω','℮','¼','½','¾','⅛','⅜','⅝','⅞','∂','∆','∏','∑','−','∕','√','∞','∫','≈','≠','≤','≥','□','◊','▫','`','´','·','˚','˙','΅','▪','▫','•●','◦','‘','’','‛','“','”','′','„','˛','˝','ˆ','ˇ','ˉ','˘','˜','′','ﬁ','ﬂ','Ǽ','æ','Ά','à','í','ä','å','Ā','Ă','ă','Ą','ǽ','Ạ','ạ','Ả','ả','Ấ','ấ','Ầ','ầ','Ẩ','ẩ','Ẫ','ẫ','Ậ','ậ','Ắ','ắ','Ằ','ằ','Ẳ','ẳ','Ẵ','ẵ','Ặ','ặ','à','á','í','ä','å','ā','æ','ą','Ǻ','ǻ','Ǽ','ǽ','Ẹ','ẹ','Ẻ','ẻ','Ẽ','ẽ','Ế','ế','Ề','ề','Ể','ể','Ễ','ễ','Ệ','Έ','ệ','è','É','ê','ë','Ỉ','ỉ','Ị','ị','ì','î','ï','Ọ','ọ','Ỏ','ỏ','Ố','ố','Ồ','ồ','Ổ','ỗ','Ộ','ộ','Ớ','ớ','Ờ','ờ','Ở','ở','Ỡ','ỡ','Ợ','ợ','Ụ','ụ','Ủ','ủ','Ứ','ứ','Ừ','ừ','Ử','ử','Ữ','ữ','Ự','ự','Ỳ','ỳ','Ỵ','ỵ','Ỷ','ỷ','Ỹ','ỹ','ð','ò','Ó','ô','õ','ö','ø','ù','Ú','û','ü','ý','þ','ß','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','÷','ø','ù','ú','û','ü','ý','þ','ÿ','ç','Ć','ć','Ĉ','ĉ','Ċ','ċ','Č','č','Ď','ď','Đ','đ','Ē','ē','Ĕ','ĕ','Ė','ė','Ę','ę','Ě','ě','Ĝ','ĝ','Ğ','ğ','Ġ','ġ','Ģ','ģ','Ĥ','ĥ','Ħ','ħ','Ĩ','ĩ','Ī','ī','Ĭ','ĭ','Į','į','İ','ı','Ĳ','ĳ','Ĵ','ĵ','Ķ','ķ','ĸ','Ĺ','ĺ','Ļ','ļ','Ľ','ľ','Ŀ','ŀ','Ł','ł','Ń','ń','Ņ','ņ','Ň','ň','ŉ','Ŋ','ŋ','Ō','ō','Ŏ','ŏ','Ő','ő','œ','œ','Ŕ','ŕ','Ŗ','ŗ','Ř','ř','Ś','ś','Ŝ','ŝ','Ş','ş','š','š','Ţ','ţ','Ť','ť','Ŧ','ŧ','Ũ','ũ','Ū','ū','Ŭ','ŭ','Ů','ů','Ű','ű','Ų','ų','Ŵ','ŵ','Ŷ','ŷ','ÿ','Ź','ź','Ż','ż','Ž','ž','ſ','ƒ','Ǻ','ǻ','Ǽ','ǽ','Ǿ','ǿ','à','í','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ô','õ','ö','÷','ø','ù','û','ü','ý','þ','ÿ','Ā','ā','Ă','ă','Ą','ą','Ć','ć','Ĉ','ĉ','Ċ','ċ','Č','٠','Ẁ','ẁ','Ẃ','ẃ','Ẅ','ẅ','Ạ','ạ','Ả','','','','','','','Ь','Э','Ю','Я','а','б','в','г','д','е','ж','з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','я','ё','ђ','ѓ','є','ѕ','і','ї','ј','љ','њ','ћ','ќ','ў','џ','Ґ','♣','p','!','œ','œ','Ŕ','ŕ','Ŗ','ŗ','Ř','ř','Ś','ś','Ŝ','ŝ','Ş','ş','š','š','Ţ','ţ','Ť','ť','Ŧ','ŧ','Ũ','ũ','Ū','ū','Ŭ','ŭ','Ů','ů','Ű','ű','Ų','ų','Ŵ','ŵ','Ŷ','ŷ','ÿ','Ź','ź','Ż','ż','Ž','ž','ſ','ƒ','Ǿ','ǿ','ˆ','ˇ','ˉ','˘','˙','˚','˛','˜','˝',';','΄','΅','Ά','·','Έ','Ή','Ί','Ό','Ύ','Ώ','ΐ','α','β','γ','δ','ε','ζ','η','θ','ι','λ','κ','μ','ν','ξ','ο','π','ρ','σ','τ','υ','φ','χ','ψ','ω','Ϊ','Ϋ','ά','έ','ή','ί','ΰ','α','β','γ','δ','ε','ζ','η','θ','ι','κ','λ','μ','ν','ξ','ο','π','ρ','ς','σ','τ','υ','φ','χ','ψ','ω','ϊ','ϋ','ό','ύ','ώ','Ё','Ђ','Ѓ','Є','Ѕ','І','Ї','Ј','Љ','Њ','Ћ','Ќ','Ў','Џ','А','Б','В','Г','Д','Е','Ж','З','И','Й','К','Л','М','Н','О','П','Р','С','Т','Ф','Х','Ц','Ч','Ш','Щ','Ъ','Ы','Ь','Э','Ю','Я'];
        $reemplazar = ['H','I','O','Y','','','','','','','','','','','','','','','','','','','I','Y','a','e','n','í','u','a','b','y','o','e','','n','O','','k','','u','E','','p','c','o','','o','X','','w','i','u','ó','ú','w','E','','r','E','S','І','I','J','','','','K','y','','А','','В','','A','E','','N','Ñ','K','','М','Н','О','','Р','С','Т','y','o','Х','','','','','b','','b','E','','R','а','б','B','r','A','е','','','N','Ñ','K','n','M','H','о','','р','с','t','у','o','x','','','','','','','','','','R','e','','r','','s','i','j','','','','K','y','','','r','W','w','W','w','w','W','Y','y','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','A','a','í','a','a','A','A','a','A','','A','a','A','a','A','a','A','a','A','a','A','a','A','a','A','a','A','a','A','a','A','a','A','a','a','á','i','a','a','a','','a','A','a','','','E','E','E','e','E','e','E','e','E','e','E','e','E','e','E','E','e','é','É','e','e','I','i','I','i','i','i','i','O','o','O','o','O','o','O','o','O','o','O','o','O','o','O','o','O','o','O','o','O','o','U','u','U','u','U','ú','U','ú','U','u','U','u','U','u','Y','y','Y','y','Y','y','Y','y','o','ó','Ó','o','o','o','o','ú','Ú','u','u','y','','','ç','é','é','e','e','i','í','i','i','o','ñ','ó','ó','ó','ó','ó','','o','ú','ú','u','u','y','','y','c','c','c','c','c','c','c','c','c','Ď','ď','Đ','đ','e','e','e','e','e','e','e','e','e','e','g','g','g','g','g','g','g','g','h','h','','','i','i','i','i','i','i','i','i','i','i','j','j','j','j','k','k','k','l','l','l','l','l','l','l','l','l','l','n','n','n','n','n','n','n','n','n','o','o','o','o','o','o','','','r','r','r','r','r','r','s','s','s','s','s','s','s','s','t','t','t','t','t','t','u','u','u','u','u','u','u','u','u','u','u','u','w','w','y','y','y','z','z','z','z','z','z','','f','a','a','','','o','o','a','í','a','a','','','e','é','e','e','i','í','i','i','','ñ','o','o','o','o','','o','u','u','u','y','','y','a','a','a','a','a','a','c','c','c','c','c','c','c','','w','w','w','w','w','w','a','a','a','','','','','','','','','','','a','','B','','','e','','','n','Ñ','k','','M','H','o','','p','c','','y','o','x','','','','','','','','','','R','e','','','','s','i','','j','','','','k','y','','','','p','','','','R','r','R','r','r','r','','','','','','','','','t','t','t','t','t','t','','','','','','','','','','','','','W','W','y','y','y','z','z','z','z','z','z','','','o','o','','','','','','','','','','','','','A','','E','H','I','O','Y','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','v','','o','','','','','','','','','','','','','ó','ú','w','E','','','E','S','I','I','J','','','','K','y','','A','','B','','','E','','','N','Ñ','K','','M','H','O','','P','C','T','','X','','','','','b','','b','','','R'];
        return str_replace($buscar, $reemplazar, $string);
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
