<?php

namespace Application\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\Sql\Select;

class PaginasBotonesTable
{
    protected $tableGateway;
    protected $adapter;
    protected $config;
    public function __construct(TableGatewayInterface $tableGateway, $adapter, $config)
    {
        $this->tableGateway = $tableGateway;
        $this->adapter = $adapter;
        $this->config = $config;
    }
    public function obtenerDatoPaginasBotones($where)
    {
        $rowset = $this->tableGateway->select($where);
        return $rowset->current();
    }
    public function obtenerDatosPaginasBotones($where)
    {
        $rowset = $this->tableGateway->select($where);
        return $rowset->toArray();
    }
    public function actualizarDatosPaginasBotones($data, $where)
    {
        return $this->tableGateway->update($data, $where);
    }
    public function agregarPaginasBotones($data)
    {
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }
    public function eliminarPaginasBotones($idpaginasbotones)
    {
        $this->tableGateway->delete(["idpaginasbotones" => $idpaginasbotones]);
    }
    public function asignarPlantillaBotones($totalBotones, $idpaginas, $idferias, $url)
    {
        $data = [
            'idpaginas' => $idpaginas,
            'idferias' => $idferias
        ];
        switch ($url) {
            case 'hall-conferencias':
                $data['configuracion'] = json_encode($this->config['plantilla_paginas_conferencias']);
                break;
            case 'hall':
            case 'recepcion':
                if ($totalBotones) {
                    $botonPlantillas = [];
                    for ($i = 1; $i <= $totalBotones; $i++) {
                        $botonPlantillas[] = $this->config['plantilla_paginas_botones'];
                    }
                    $data['configuracion'] = json_encode($botonPlantillas);
                }
                break;
            default:
                break;
        }

        $this->agregarPaginasBotones($data);
    }
}
