<?php

namespace Application\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\Sql\Select;

class ClientesTable
{
    protected $tableGateway;
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    public function obtenerClientes()
    {
        $condiciones = [];
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT c.*, CONCAT(u.nombres, ' ', u.apellido_paterno, ' ', u.apellido_materno) AS usuario
        FROM clientes c
        LEFT JOIN fd_usuarios u ON u.idusuario = c.idusuario";
        if (! empty($condiciones)) {
            $sql .= " WHERE " . implode(" AND ", $condiciones);
        }
        $sql .= " ORDER BY c.idclientes DESC";
        return $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
    }
    public function obtenerDatoClientes($where)
    {
        $rowset = $this->tableGateway->select($where);
        return $rowset->current();
    }
    public function actualizarDatosClientes($data, $idclientes)
    {
        $rowset = $this->tableGateway->update($data, ["idclientes" => $idclientes]);
    }
    public function agregarClientes($data)
    {
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }
    public function eliminarClientes($idclientes)
    {
        $this->tableGateway->delete(["idclientes" => $idclientes]);
    }
    public function obtenerFiltroDatosClientes($start, $length, $search = null, $totalregistro = false)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM clientes";
        $sql .= " ORDER BY idclientes ASC";
        if (! $totalregistro) {
            $sql .= " LIMIT {$start},{$length}";
        }
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
}
