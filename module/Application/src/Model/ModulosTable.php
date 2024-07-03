<?php

namespace Application\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Expression;

class ModulosTable
{
    protected $tableGateway;

    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    public function obtenerDatoModulos($idmodulos)
    {
        $rowset = $this->tableGateway->select(['idmodulos' => $idmodulos]);
        return $rowset->current();
    }
    public function actualizarDatosModulos($data, $idmodulos)
    {
        $rowset = $this->tableGateway->update($data, ['idmodulos' => $idmodulos]);
    }
    public function obtenerDatosModulos()
    {
        $sqlSelect = $this->tableGateway->getSql()->select();
        $rowset = $this->tableGateway->selectWith($sqlSelect);
        return $rowset->toArray();
    }
    public function agregarModulo($data)
    {
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }
    public function eliminarModulo($idmodulos)
    {
        $this->tableGateway->delete(['idmodulos' => $idmodulos]);
    }
}
