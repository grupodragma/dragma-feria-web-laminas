<?php

namespace Application\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\Sql\Select;

class EmpresasTable
{
    protected $tableGateway;
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    public function obtenerEmpresas($idferias, $idperfil, $idempresas = null, $encargado = null)
    {
        $condiciones = [];
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT e.*, z.nombre AS zona, s.nombre AS stand, CONCAT(IFNULL(ex.nombres, ''),' ', IFNULL(ex.apellido_paterno, ''),' ', IFNULL(ex.apellido_materno, '')) AS expositor
        FROM empresas e
        LEFT JOIN zonas z ON z.idzonas = e.idzonas 
        LEFT JOIN stand s ON s.idstand = e.idstand
        LEFT JOIN expositores ex ON ex.idexpositores = e.idexpositores";
        if ($idperfil != 1 && $idferias != null) {
            $condiciones[] = "z.idferias = {$idferias}";
        }
        if ($idperfil != 1 && $idempresas != 0 && $encargado != 1) {
            $condiciones[] = "e.idempresas = {$idempresas}";
        }
        if (! empty($condiciones)) {
            $sql .= " WHERE " . implode(" AND ", $condiciones);
        }
        $sql .= " ORDER BY e.idempresas DESC";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
    public function obtenerDatosZonasEmpresas($idferias, $idperfil)
    {
        $condiciones = [];
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT e.*, z.nombre AS zona, z.orden AS zona_orden FROM empresas e LEFT JOIN zonas z ON z.idzonas = e.idzonas";
        if ($idperfil != 1 && $idferias != null) {
            $condiciones[] = "z.idferias = {$idferias}";
        }
        if (! empty($condiciones)) {
            $sql .= " WHERE " . implode(" AND ", $condiciones);
        }
        $sql .= " ORDER BY e.nombre ASC";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
    public function obtenerEmpresasBannerOpciones($idferias)
    {
        $condiciones = [];
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT e.*, z.nombre AS zona, z.orden AS zona_orden FROM empresas e LEFT JOIN zonas z ON z.idzonas = e.idzonas";
        $condiciones[] = "z.idferias = {$idferias}";
        if (! empty($condiciones)) {
            $sql .= " WHERE " . implode(" AND ", $condiciones);
        }
        $sql .= " ORDER BY z.orden, e.orden ASC";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
    public function obtenerDatoEmpresas($where)
    {
        $rowset = $this->tableGateway->select($where);
        return $rowset->current();
    }
    public function obtenerDatosEmpresas($where)
    {
        $rowset = $this->tableGateway->select($where);
        return $rowset->toArray();
    }
    public function obtenerEmpresasGalerias($idzonas)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM empresas e
        LEFT JOIN stand_galeria sg ON sg.idstandgaleria = e.idstandgaleria";
        $sql .= " WHERE e.idzonas = {$idzonas}";
        $sql .= " ORDER BY e.orden ASC";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
    public function obtenerTotalEmpresasPorFeria($idferias, $idzonas)
    {
        $condiciones = [];
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT *
        FROM empresas e
        LEFT JOIN zonas z ON z.idzonas = e.idzonas";
        if ($idzonas != null) {
            $condiciones[] = "z.idzonas = {$idzonas}";
        }
        if ($idferias != null) {
            $condiciones[] = "z.idferias = {$idferias}";
        }
        if (! empty($condiciones)) {
            $sql .= " WHERE " . implode(" AND ", $condiciones);
        }
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return count($data);
    }
    public function actualizarDatosEmpresas($data, $idempresas)
    {
        $rowset = $this->tableGateway->update($data, ["idempresas" => $idempresas]);
    }
    public function agregarEmpresas($data)
    {
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }
    public function eliminarEmpresas($idempresas)
    {
        $this->tableGateway->delete(["idempresas" => $idempresas]);
    }
    public function obtenerFiltroDatosEmpresas($start, $length, $search = null, $totalregistro = false)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM empresas";
        $sql .= " ORDER BY idempresas ASC";
        if (! $totalregistro) {
            $sql .= " LIMIT {$start},{$length}";
        }
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
    public function obtenerUltimoOrdenEmpresasPorZona($idzonas)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT IF((MAX(orden) + 1), (MAX(orden) + 1), 1) AS siguiente_orden FROM empresas WHERE idzonas = {$idzonas}";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->current();
        return $data['siguiente_orden'];
    }
    public function reordenarOrdenEmpresasPorZona($idzonas)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM empresas WHERE idzonas = {$idzonas} ORDER BY orden ASC";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        if (! empty($data)) {
            $i = 1;
            foreach ($data as $item) {
                $this->actualizarDatosEmpresas(['orden' => $i], $item['idempresas']);
                $i++;
            }
        }
    }
    public function graficoTotalClickEmpresas($idferias = null, $fecha = null)
    {
        if ($fecha != null) {
            $fechas = explode(" - ", $fecha);
            $fecha_inicio = date('Y-m-d', strtotime(str_replace("/", "-", $fechas[0])));
            $fecha_fin = date('Y-m-d', strtotime(str_replace("/", "-", $fechas[1])));
        }
        $condiciones = [];
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT ue.*, COUNT(*) AS total_click, e.nombre AS label
        FROM usuario_eventos ue
        LEFT JOIN empresas e ON e.idempresas = ue.idempresas";
        $condiciones[] = "ue.empresas = 1";
        $condiciones[] = "ue.idempresas != 0";
        $condiciones[] = "ue.idvisitantes != 0";
        $condiciones[] = "ue.idexpositores = 0";
        if ($fecha != null) {
            $condiciones[] = "ue.fecha_registro >= '{$fecha_inicio} 00:00:00' AND ue.fecha_registro <= '{$fecha_fin} 23:59:59'";
        }
        if ($idferias != null) {
            $condiciones[] = "ue.idferias = {$idferias}";
        }
        if (! empty($condiciones)) {
            $sql .= " WHERE " . implode(" AND ", $condiciones);
        }
        $sql .= " GROUP BY ue.idempresas";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
    public function obtenerEmpresasPorFeria($idferias)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT e.* FROM empresas e
        INNER JOIN zonas z on z.idzonas = e.idzonas
        WHERE z.idferias = {$idferias}
        ORDER BY e.nombre ASC";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
}
