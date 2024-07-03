<?php

namespace Application\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\Sql\Select;
use Laminas\Db\TableGateway\TableGateway;

class PerfilesTable
{
    protected $tableGateway;
    /*Iniciadlizador de Modelo, con instancia al adaptador de Base de datos*/
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    public function obtenerTodosPerfiles()
    {
        $recordSet = $this->tableGateway->select();
        return $recordSet->toArray();
    }
    public function obtenerDatosPerfil($idperfil)
    {
        $recordSet = $this->tableGateway->select(['idperfil' => $idperfil]);
        return $recordSet->current();
    }
    public function obtenerTodosPerfilesyModulos()
    {
        $recordSet = $this->tableGateway->select(function (Select $select) {

            $select->join('fd_perfiles_modulos', 'fd_perfiles.idperfil = fd_perfiles_modulos.idperfil', [], 'left');
            $select->join('fd_submodulos', 'fd_submodulos.idsubmodulo = fd_perfiles_modulos.idsubmodulo', ['nombre_submodulo' => 'nombre','icono_submodulo' => 'icono','idsubmodulo'], 'left');
            $select->join('fd_modulos', 'fd_submodulos.idmodulo = fd_modulos.idmodulos', ['nombre_modulo' => 'nombre','icono_modulo' => 'icono','idmodulos'], 'left');
            $select->join('fd_usuarios', 'fd_usuarios.idperfil = fd_perfiles.idperfil', ['nombre_usuario' => 'nombres','apellido_paterno','apellido_materno','idusuario'], 'left');
        })->toArray();
        $perfiles = [];
        foreach ($recordSet as $record) {
            if (! isset($perfiles[$record['idperfil']])) {
                $perfiles[$record['idperfil']] = [
                    'idperfil' => $record['idperfil'],
                    'nombre' => $record['nombre'],
                    'descripcion' => $record['descripcion'],
                    'modulos' => [],
                    'usuarios' => []
                ];
            }
            if (! isset($perfiles[$record['idperfil']]['usuarios'][$record['idusuario']]) && (! empty($record['nombre_usuario']) || ! empty($record['apellido_paterno']))) {
                $perfiles[$record['idperfil']]['usuarios'][$record['idusuario']] = [
                    'nombre' => $record['nombre_usuario'],
                    'apellido_paterno' => $record['apellido_paterno'],
                    'apellido_materno' => $record['apellido_materno']
                ];
            }
            if (! isset($perfiles[$record['idperfil']]['modulos'][$record['idmodulos']]) && ! empty($record['nombre_modulo'])) {
                $perfiles[$record['idperfil']]['modulos'][$record['idmodulos']] = [
                    'nombre' => $record['nombre_modulo'],
                    'icono' => $record['icono_modulo'],
                    'submodulos' => []
                ];
            }
            $perfiles[$record['idperfil']]['modulos'][$record['idmodulos']]['submodulos'][$record['idsubmodulo']] = [
               'nombre' => $record['nombre_submodulo'],
               'icono' => $record['icono_submodulo']
            ];
        }
        return $perfiles;
    }
    public function obtenerTodosModulos()
    {
        $adapter = $this->tableGateway->getAdapter();
        $modulosTable = new TableGateway('fd_modulos', $adapter);
        $recordSet = $modulosTable->select(function (Select $select) {

            $select->join('fd_submodulos', 'fd_submodulos.idmodulo = fd_modulos.idmodulos', ['nombre_submodulo' => 'nombre','icono_submodulo' => 'icono','idsubmodulo']);
        })->toArray();
        return $recordSet;
    }
    public function agregarPerfil($datos_perfil, $datos_submodulos)
    {
        $this->tableGateway->insert($datos_perfil);
        $id_perfil = $this->tableGateway->lastInsertValue;
        $adapter = $this->tableGateway->getAdapter();
        $perfilesModulosTable = new TableGateway('fd_perfiles_modulos', $adapter);
        if ($datos_submodulos == null) {
            $datos_submodulos = [];
        }
        foreach ($datos_submodulos as $idsubmodulo) {
            $data = [
                'idperfil' => $id_perfil,
                'idsubmodulo' => $idsubmodulo
            ];
            $perfilesModulosTable->insert($data);
        }
    }
    public function obtenerPerfilyModulos($idperfil)
    {
        $dataPerfil = $this->tableGateway->select(['idperfil' => $idperfil])->current();
        $adaptter = $this->tableGateway->getAdapter();
        $perfilesModulosTable = new TableGateway('fd_perfiles_modulos', $adaptter);
        $dataModulos = $perfilesModulosTable->select(['idperfil' => $idperfil], function (Select $select) {

            $select->join('fd_submodulos', 'fd_submodulos.idsubmodulo = fd_perfiles_modulos.idsubmodulo', ['idsubmodulo']);
        })->toArray();
        return ['dataPerfil' => $dataPerfil,'dataModulos' => $dataModulos];
    }
    public function editarPerfil($datos_perfil, $datos_submodulos, $idperfil)
    {
        $this->tableGateway->update($datos_perfil, ['idperfil' => $idperfil]);
        $adapter = $this->tableGateway->getAdapter();
        $perfilesModulosTable = new TableGateway('fd_perfiles_modulos', $adapter);
        $perfilesModulosTable->delete(['idperfil' => $idperfil]);
        foreach ($datos_submodulos as $idsubmodulo) {
            $data = [
                'idperfil' => $idperfil,
                'idsubmodulo' => $idsubmodulo
            ];
            $perfilesModulosTable->insert($data);
        }
    }
    public function eliminarPerfil($idperfil)
    {
        if ($idperfil == 1) {
            return;
        }
        $this->tableGateway->delete(['idperfil' => $idperfil]);
        $adapter = $this->tableGateway->getAdapter();
        $perfilesModulosTable = new TableGateway('fd_perfiles_modulos', $adapter);
        $perfilesModulosTable->delete(['idperfil' => $idperfil]);
    }
}
