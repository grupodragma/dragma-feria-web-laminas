<?php

namespace Application\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;

class AccesoTable
{
    protected $tableGateway;

    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function verificarUsuario($usuario, $password)
    {
        $adapter = $this->tableGateway->getAdapter();
        $salt = '::::::(`_´)::::: NCL/SECURE';
        $password = md5($salt . $password);
        $resultSet = $adapter->query("SELECT u.*, f.nombre AS feria FROM fd_usuarios u LEFT JOIN ferias f ON f.idferias = u.idferias WHERE u.usuario='{$usuario}' AND u.contrasena='{$password}'");
        $result = $resultSet->execute();
        return $result->current();
    }

    public function obtenerMenuUsuario($sessionUsuario, $uri_parts)
    {
        $adapter = $this->tableGateway->getAdapter();
        $resultSet = $adapter->query("SELECT sm.*, mo.ruta AS modulo_ruta, mo.nombre as modulo_nombre, mo.icono as modulo_icono FROM fd_perfiles_modulos as ps INNER JOIN fd_submodulos AS sm ON sm.idsubmodulo = ps.idsubmodulo INNER JOIN fd_modulos AS mo ON mo.idmodulos = sm.idmodulo WHERE idperfil='{$sessionUsuario['idperfil']}' ORDER BY mo.pos_grupo_dash ASC, sm.orden ASC");
        $result = $resultSet->execute();
        $modulos = [];
        $breakcums = [];
        $modulos[0] = [
            'nombre' => 'Dashboard',
            'ruta' => 'panel',
            'icono' => 'fas fa-desktop',
            'submodulos' => [],
            'activo_class' => '',
            'endash_board' => false
        ];
        if ($uri_parts[0] == 'panel') {
            $modulos[0]['activo_class'] = 'active';
            $breakcums['module'] = $modulos[0]['nombre'];
            $breakcums['icono'] = $modulos[0]['icono'];
        }
        foreach ($result as $row) {
            if (! array_key_exists($row['idmodulo'], $modulos)) {
                $modulo = [
                    'nombre' => $row['modulo_nombre'],
                    'ruta' => $row['modulo_ruta'],
                    'icono' => $row['modulo_icono'],
                    'submodulos' => [],
                    'activo_class' => '',
                    'endash_board' => false
                ];
                $modulos[$row['idmodulo']] = $modulo;
                if ($uri_parts[0] == $row['modulo_ruta']) {
                    $modulos[$row['idmodulo']]['activo_class'] = 'active';
                    $breakcums['module'] = $modulos[$row['idmodulo']]['nombre'];
                    $breakcums['icono'] = $modulos[$row['idmodulo']]['nombre'];
                }
            }
            $modulos[$row['idmodulo']]['submodulos'][$row['idsubmodulo']] = [
                'nombre' => $row['nombre'],
                'ruta' => $row['ruta'],
                'icono' => $row['icono'],
                'activo_class' => ''
            ];
            if (isset($uri_parts[1])) {
                if ($uri_parts[1] == $row['ruta']) {
                    $modulos[$row['idmodulo']]['submodulos'][$row['idsubmodulo']]['activo_class'] = 'active';
                    $breakcums['submodule'] = $modulos[$row['idmodulo']]['submodulos'][$row['idsubmodulo']]['nombre'];
                    $breakcums['icono'] = $modulos[$row['idmodulo']]['submodulos'][$row['idsubmodulo']]['icono'];
                }
            }
        }
        return [$modulos,$breakcums];
    }

    /*public function obtenerDashboardUsuario($sessionUsuario){
        $adapter = $this->tableGateway->getAdapter();
        $resultSet = $adapter->query("SELECT sm.*, mo.ruta AS modulo_ruta, mo.nombre as modulo_nombre, mo.icono as modulo_icono, mo.col_grupo_dash as col, mo.pos_grupo_dash as pos FROM fd_perfiles_modulos as ps INNER JOIN fd_submodulos AS sm ON sm.idsubmodulo = ps.idsubmodulo INNER JOIN fd_modulos AS mo ON mo.idmodulos = sm.idmodulo WHERE idperfil='{$sessionUsuario['idperfil']}' ORDER BY  mo.col_grupo_dash, mo.pos_grupo_dash ASC");
        $result = $resultSet->execute();

        $bloques = [
            "1"=>[],
            "2"=>[],
            "3"=>[]
        ];

        foreach($result as $row){
            if($row['visible_grupo_dash'] != '1')continue;
            if(!isset($bloques[$row['col']][$row['idmodulo']])){
                $bloques[$row['col']][$row['idmodulo']] = [
                    'nombre'=>$row['modulo_nombre'],
                    'icon'=>$row['modulo_icono'],
                    'submodulos'=>[]
                ];
            }
            $bloques[$row['col']][$row['idmodulo']]['submodulos'][] = [
                'nombre'=>$row['nombre'],
                'descripcion'=>$row['desc_grupo_dash'],
                'icono'=>$row['icono'],
                'ruta'=>'/'.$row['modulo_ruta'].'/'.$row['ruta']
            ];
        }
        return $bloques;
    }*/

    public function obtenerModuloById($idmodulo)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM fd_modulos WHERE idmodulos='{$idmodulo}'";
        return $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->current();
    }

    public function obtenerDashboardUsuario($sessionUsuario)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT sm.*, mo.ruta AS modulo_ruta, mo.nombre as modulo_nombre, mo.icono as modulo_icono, mo.col_grupo_dash as col, mo.pos_grupo_dash as pos FROM fd_perfiles_modulos as ps INNER JOIN fd_submodulos AS sm ON sm.idsubmodulo = ps.idsubmodulo INNER JOIN fd_modulos AS mo ON mo.idmodulos = sm.idmodulo WHERE idperfil='{$sessionUsuario['idperfil']}' ORDER BY mo.pos_grupo_dash, sm.orden ASC";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        $result = [];
        foreach ($data as $item) {
            $result[$item['idmodulo']]['submodulos'][] = $item;
        }
        $response = [];
        if (! empty($result)) {
            foreach ($result as $idmodulo => $item) {
                $dataItem = [];
                $dataItem['modulo'] = $this->obtenerModuloById($idmodulo);
                $dataItem['submodulos'] = $item['submodulos'];
                $response[] = $dataItem;
            }
        }
        return $response;
    }
}
