<?php

namespace Application\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\Sql\Select;

class FeriasTable {
    protected $tableGateway;
    public function __construct(TableGatewayInterface $tableGateway) {
        $this->tableGateway = $tableGateway;
    }
    public function obtenerFerias(){
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT f.*, c.nombre as cliente, p.nombre as plan FROM ferias f
        LEFT JOIN clientes c ON c.idclientes = f.idclientes
        LEFT JOIN planes p ON p.idplanes = f.idplanes
        ORDER BY f.idferias DESC";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
    public function obtenerDatoFerias($where){
        $rowset = $this->tableGateway->select($where);
        return $rowset->current();
    }
    public function actualizarDatosFerias($data,$idferias){
        $rowset = $this->tableGateway->update($data,["idferias" => $idferias]);
    }
    public function agregarFerias($data){
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }
    public function eliminarFerias($idferias){
        $this->tableGateway->delete(["idferias" => $idferias]);
    }
    public function obtenerFiltroDatosFerias($start,$length,$search=null,$totalregistro=false){
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM ferias";
        $sql .= " ORDER BY idferias ASC";
        if(!$totalregistro)$sql .= " LIMIT {$start},{$length}";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
    public function obtenerPasoSecuencia($idferias){
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT *,
            (SELECT nombre FROM menus WHERE idmenus = fp.paso_1_idmenu) AS paso_1_menu,
            (SELECT hash_url FROM menus WHERE idmenus = fp.paso_1_idmenu) AS paso_1_menu_url,
            (SELECT posicion FROM menus WHERE idmenus = fp.paso_1_idmenu) AS paso_1_menu_posicion,
            (SELECT nombre FROM menus WHERE idmenus = fp.paso_2_idmenu) AS paso_2_menu,
            (SELECT hash_url FROM menus WHERE idmenus = fp.paso_2_idmenu) AS paso_2_menu_url,
            (SELECT posicion FROM menus WHERE idmenus = fp.paso_2_idmenu) AS paso_2_menu_posicion,
            (SELECT nombre FROM menus WHERE idmenus = fp.paso_3_idmenu) AS paso_3_menu,
            (SELECT hash_url FROM menus WHERE idmenus = fp.paso_3_idmenu) AS paso_3_menu_url,
            (SELECT posicion FROM menus WHERE idmenus = fp.paso_3_idmenu) AS paso_3_menu_posicion
        FROM ferias_pasos fp
        WHERE fp.idferias = $idferias";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->current();
        if(!$data || !(int)$data['paso_1'] && !(int)$data['paso_2'] && !(int)$data['paso_3']) {
            return false;
        }
        return [
            'home'=> [
                'estado'=> $data['paso_1'],
                'idmenu'=> $data['paso_1_idmenu'],
                'menu'=> $data['paso_1_menu'],
                'menu_url'=> $data['paso_1_menu_url'],
                'menu_posicion'=> $data['paso_1_menu_posicion']
            ],
            'registro'=> [
                'estado'=> $data['paso_2'],
                'idmenu'=> $data['paso_2_idmenu'],
                'menu'=> $data['paso_2_menu'],
                'menu_url'=> $data['paso_2_menu_url'],
                'menu_posicion'=> $data['paso_2_menu_posicion']
            ],
            'contenido'=> [
                'estado'=> $data['paso_3'],
                'idmenu'=> $data['paso_3_idmenu'],
                'menu'=> $data['paso_3_menu'],
                'menu_url'=> $data['paso_3_menu_url'],
                'menu_posicion'=> $data['paso_3_menu_posicion']
            ]
        ];
    }
}