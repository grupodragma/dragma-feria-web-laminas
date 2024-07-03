<?php

namespace Application\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\Sql\Select;

class ZonasTable
{
    protected $tableGateway;
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    public function obtenerZonas($idferias, $idperfil)
    {
        $condiciones = [];
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT z.*, f.nombre AS feria FROM zonas z
        LEFT JOIN ferias f ON f.idferias = z.idferias";
        if ($idperfil != 1 && $idferias != null) {
            $condiciones[] = "f.idferias = {$idferias}";
        }
        if (! empty($condiciones)) {
            $sql .= " WHERE " . implode(" AND ", $condiciones);
        }
        $sql .= " ORDER BY z.idzonas DESC";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
    public function obtenerDatosZonas($idferias)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM zonas WHERE idferias = {$idferias}";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
    public function obtenerTotalZonasPorFeria($idferias)
    {
        $rowset = $this->tableGateway->select(['idferias' => $idferias]);
        return count($rowset->toArray());
    }
    public function obtenerDatoZonas($where)
    {
        $rowset = $this->tableGateway->select($where);
        return $rowset->current();
    }
    public function actualizarDatosZonas($data, $idzonas)
    {
        $rowset = $this->tableGateway->update($data, ["idzonas" => $idzonas]);
    }
    public function agregarZonas($data)
    {
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }
    public function eliminarZonas($idzonas)
    {
        $this->tableGateway->delete(["idzonas" => $idzonas]);
    }
    public function obtenerFiltroDatosZonas($start, $length, $search = null, $totalregistro = false)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM zonas";
        $sql .= " ORDER BY idzonas ASC";
        if (! $totalregistro) {
            $sql .= " LIMIT {$start},{$length}";
        }
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
    public function obtenerUltimoOrdenZonasPorFeria($idferias)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT IF((MAX(orden) + 1), (MAX(orden) + 1), 1) AS siguiente_orden FROM zonas WHERE idferias = {$idferias}";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->current();
        return $data['siguiente_orden'];
    }
    public function reordenarOrdenZonasPorFeria($idferias)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM zonas WHERE idferias = {$idferias} ORDER BY orden ASC";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        if (! empty($data)) {
            $i = 1;
            foreach ($data as $item) {
                $this->actualizarDatosZonas(['orden' => $i], $item['idzonas']);
                $i++;
            }
        }
    }
    public function graficoTotalClickZonas($idferias = null, $fecha = null)
    {
        if ($fecha != null) {
            $fechas = explode(" - ", $fecha);
            $fecha_inicio = date('Y-m-d', strtotime(str_replace("/", "-", $fechas[0])));
            $fecha_fin = date('Y-m-d', strtotime(str_replace("/", "-", $fechas[1])));
        }
        $condiciones = [];
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT ue.*, COUNT(*) AS total_click, z.nombre AS label
        FROM usuario_eventos ue
        LEFT JOIN zonas z ON z.idzonas = ue.idzonas";
        $condiciones[] = "ue.zonas = 1";
        $condiciones[] = "ue.idzonas != 0";
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
        $sql .= " GROUP BY ue.idzonas";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
}
