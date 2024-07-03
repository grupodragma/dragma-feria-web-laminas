<?php

namespace Application\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\Sql\Select;

class PromocionesTable
{
    protected $tableGateway;
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    public function obtenerPromociones($idferias = null, $idperfil = null, $idusuario = null, $idferiaspromocion = null, $encargado = null)
    {
        $condiciones = [];
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT p.*, e.nombre AS empresa, CONCAT(u.nombres, ' ', u.apellido_paterno, ' ', u.apellido_materno) AS usuario
        FROM promociones p
        LEFT JOIN empresas e ON e.idempresas = p.idempresas
        LEFT JOIN zonas z ON z.idzonas = e.idzonas
        LEFT JOIN fd_usuarios u ON u.idusuario = p.idusuario";
        if ($idperfil != 1 && $idferias != null) {
            $condiciones[] = "z.idferias = {$idferias}";
        }
        if ($idperfil != 1 && $idusuario != null && $encargado != 1) {
            $condiciones[] = "p.idusuario = {$idusuario}";
        }
        //Solo para filtrar las promociones por feria
        if ($idferiaspromocion != null) {
            $condiciones[] = "z.idferias = {$idferiaspromocion}";
        }
        if (! empty($condiciones)) {
            $sql .= " WHERE " . implode(" AND ", $condiciones);
        }
        $sql .= " ORDER BY p.idpromociones DESC";
        return $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
    }
    public function obtenerDatoPromociones($where)
    {
        $rowset = $this->tableGateway->select($where);
        return $rowset->current();
    }
    public function actualizarDatosPromociones($data, $idpromociones)
    {
        $rowset = $this->tableGateway->update($data, ["idpromociones" => $idpromociones]);
    }
    public function agregarPromociones($data)
    {
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }
    public function eliminarPromociones($idpromociones)
    {
        $this->tableGateway->delete(["idpromociones" => $idpromociones]);
    }
    public function obtenerFiltroDatosPromociones($start, $length, $search = null, $totalregistro = false)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM promociones";
        $sql .= " ORDER BY idpromociones ASC";
        if (! $totalregistro) {
            $sql .= " LIMIT {$start},{$length}";
        }
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
}
