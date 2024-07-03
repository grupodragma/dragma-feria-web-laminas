<?php

namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\View\Model\JsonModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReporteController extends AbstractActionController
{
    protected $serviceManager;
    protected $objVisitantesRegistrosTable;
    protected $sessionContainer;

    public function __construct($serviceManager, $objVisitantesRegistrosTable)
    {
        $this->serviceManager = $serviceManager;
        $this->objVisitantesRegistrosTable = $objVisitantesRegistrosTable;
        $this->sessionContainer = $this->serviceManager->get('DatosSession')->datosUsuario;
    }

    public function visitantesRegistrosAction()
    {
        $data = [
            'usuarios' => $this->objVisitantesRegistrosTable->filtrarUsuariosPorFeria($this->sessionContainer['idperfil'], $this->sessionContainer['idferias'])
        ];
        return new ViewModel($data);
    }

    public function listarVisitantesRegistrosAction()
    {
        $tiporegistro = $this->params()->fromQuery('tiporegistro');
        $idusuarios = $this->params()->fromQuery('idusuarios');
        $dataVisitantesRegistros = $this->objVisitantesRegistrosTable->obtenerVisitantesRegistros($this->sessionContainer['idperfil'], $this->sessionContainer['idferias'], $tiporegistro, $idusuarios, $this->sessionContainer['tipo'], $this->sessionContainer['idusuario']);
        $data_out = [];
        $data_out['data'] = [];
        foreach ($dataVisitantesRegistros as $item) {
            $data_out['data'][] = [
                $item['feria'],
                date('d/m/Y H:i', strtotime($item['fecha_registro'])),
                $item['visitante'],
                $item['tipo_registro'],
                $item['usuario'],
            ];
        }
        return $this->jsonZF($data_out);
    }

    public function descargarReporteVisitantesRegistrosAction()
    {

        $tiporegistro = $this->params()->fromQuery('tiporegistro');
        $idusuarios = $this->params()->fromQuery('idusuarios');
        $dataVisitantesRegistros = $this->objVisitantesRegistrosTable->obtenerVisitantesRegistros($this->sessionContainer['idperfil'], $this->sessionContainer['idferias'], $tiporegistro, $idusuarios, $this->sessionContainer['tipo'], $this->sessionContainer['idusuario']);

        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setCellValue('A1', 'Feria');
            $sheet->setCellValue('B1', 'Fecha Registro');
            $sheet->setCellValue('C1', 'Visitante');
            $sheet->setCellValue('D1', 'Tipo');
            $sheet->setCellValue('E1', 'Usuario');
            $sheet->setCellValue('F1', 'Número Documento');
            $sheet->setCellValue('G1', 'Correo');
            $sheet->setCellValue('H1', 'Teléfono');

            $sheet->getStyle("A1:H1")->getFont()->setBold(true);

            $i = 2;

            foreach ($dataVisitantesRegistros as $item) {
                if ($this->sessionContainer['idperfil'] != "1" && $item['usuario'] == "") {
                    continue;
                }
                $sheet->setCellValue('A' . $i, $item['feria']);
                $sheet->setCellValue('B' . $i, date('d/m/Y H:i', strtotime($item['fecha_registro'])));
                $sheet->setCellValue('C' . $i, $item['visitante']);
                $sheet->setCellValue('D' . $i, $item['tipo_registro']);
                $sheet->setCellValue('E' . $i, $item['usuario']);
                $sheet->setCellValue('F' . $i, $item['numero_documento']);
                $sheet->setCellValue('G' . $i, $item['correo']);
                $sheet->setCellValue('H' . $i, $item['telefono']);
                $i++;
            }

            $file = getcwd() . '/public/tmp/reporte_visitantes_presenciales.xlsx';
            $sheet = new Xlsx($spreadsheet);
            $sheet->save($file);
            return new JsonModel(['result' => 'success', 'file' => 'reporte_visitantes_presenciales.xlsx']);
        } catch (PDOException $e) {
            return new JsonModel(['result' => 'error', 'data' => $e->getMessage()]);
        }
    }

    private function consoleZF($data)
    {
        $viewModel = new ViewModel($data);
        $viewModel->setTerminal(true);
        return $viewModel;
    }

    private function jsonZF($data)
    {
        return new JsonModel($data);
    }
}
