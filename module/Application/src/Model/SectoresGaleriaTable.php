<?php

namespace Application\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\Sql\Select;

class SectoresGaleriaTable
{
    protected $tableGateway;
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    public function obtenerSectoresGaleria()
    {
        $select = $this->tableGateway->getSql()->select();
        $select->order('idsectoresgaleria DESC');
        $rowset = $this->tableGateway->selectWith($select);
        return $rowset->toArray();
    }
    public function obtenerDatoSectoresGaleria($where)
    {
        $rowset = $this->tableGateway->select($where);
        return $rowset->current();
    }
    public function obtenerDatosSectoresGaleria($idsectores = null)
    {
        $condiciones = [];
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM sectores_galeria s";
        if ($idsectores != null) {
            $condiciones[] = "s.idsectores = {$idsectores}";
        }
        if (! empty($condiciones)) {
            $sql .= " WHERE " . implode(" AND ", $condiciones);
        }
        $sql .= " ORDER BY idsectoresgaleria DESC";
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
    public function actualizarDatosSectoresGaleria($data, $idsectoresgaleria)
    {
        $rowset = $this->tableGateway->update($data, ["idsectoresgaleria" => $idsectoresgaleria]);
    }
    public function agregarSectoresGaleria($data)
    {
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }
    public function eliminarSectoresGaleria($idsectoresgaleria)
    {
        $this->tableGateway->delete(["idsectoresgaleria" => $idsectoresgaleria]);
    }
    public function obtenerFiltroDatosSectoresGaleria($start, $length, $search = null, $totalregistro = false)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM sectores_galeria";
        $sql .= " ORDER BY idsectoresgaleria ASC";
        if (! $totalregistro) {
            $sql .= " LIMIT {$start},{$length}";
        }
        $data = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE)->toArray();
        return $data;
    }
    public function imagenPrimarioSectoresGaleria($idsectoresgaleria)
    {
        $data = $this->tableGateway->select()->toArray();
        if (! empty($data)) {
            foreach ($data as $item) {
                $primario = ( $item['idsectoresgaleria'] == $idsectoresgaleria ) ? 1 : 0;
                $this->actualizarDatosSectoresGaleria(['primario' => $primario], $item['idsectoresgaleria']);
            }
        }
    }
}
