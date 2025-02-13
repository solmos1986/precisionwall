<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Excel;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class orden_proyecto_total_detalle_Export implements
WithEvents
{
    private $proyectos;
    private $extras;
    private $fecha_inicio;
    private $fecha_fin;
    public function __construct($proyectos, $extras, $fecha_inicio, $fecha_fin)
    {
        $this->proyectos = $proyectos;
        $this->extras = $extras;
        $this->fecha_inicio = $fecha_inicio;
        $this->fecha_fin = $fecha_fin;
    }
    use Exportable;
    /**
     * @return \Illuminate\Support\Collection
     */
    public function registerEvents(): array
    {
        return [
            // Handle by a closure.
            BeforeExport::class => function (BeforeExport $event) {

                $event->writer->getProperties()->setCreator('pwt');
                $event->writer->reopen(new \Maatwebsite\Excel\Files\LocalTemporaryFile(public_path() . '/plantilla/' . 'report_materiales_detalle.xlsx'), Excel::XLSX);
                $event->writer->getSheetByIndex(0);
                // fill with information
                $event->getWriter()->getSheetByIndex(0)->setCellValue('A2', "Material and Equipment Report " . date('Y/m/d', strtotime($this->fecha_inicio)) . " to " . date('Y/m/d', strtotime($this->fecha_fin)));
                $row = 4;
                foreach ($this->proyectos as $i => $proyecto) {
                    //titulos proyectos
                    $event->getWriter()->getSheetByIndex(0)->setCellValue('A' . $row, 'GENERAL CONTRACTOR');
                    $event->getWriter()->getSheetByIndex(0)->mergeCells('B' . $row . ':D' . $row);
                    $event->getWriter()->getSheetByIndex(0)->setCellValue('B' . $row, $proyecto->nombre_empresa);

                    $row = ($row + 1);
                    $event->getWriter()->getSheetByIndex(0)->setCellValue('A' . $row, 'PRECISION WALL TECH PROJECT');
                    $event->getWriter()->getSheetByIndex(0)->mergeCells('B' . $row . ':D' . $row);
                    $event->getWriter()->getSheetByIndex(0)->setCellValue('B' . $row, $proyecto->Codigo);

                    $row = ($row + 1);
                    $event->getWriter()->getSheetByIndex(0)->setCellValue('A' . $row, 'PROJECT NAME');
                    $event->getWriter()->getSheetByIndex(0)->mergeCells('B' . $row . ':D' . $row);
                    $event->getWriter()->getSheetByIndex(0)->setCellValue('B' . $row, $proyecto->Nombre);

                    $row = ($row + 1);
                    $event->getWriter()->getSheetByIndex(0)->setCellValue('A' . $row, 'PROJECT STATUS');
                    $event->getWriter()->getSheetByIndex(0)->mergeCells('B' . $row . ':D' . $row);
                    $event->getWriter()->getSheetByIndex(0)->setCellValue('B' . $row, $proyecto->nombre_estatus);

                    $row = ($row + 1);
                    $event->getWriter()->getSheetByIndex(0)->setCellValue('A' . $row, 'PROJECT ADDRESS');
                    $event->getWriter()->getSheetByIndex(0)->mergeCells('B' . $row . ':D' . $row);
                    $event->getWriter()->getSheetByIndex(0)->setCellValue('B' . $row, $proyecto->Estado . ' ' . $proyecto->Estado . ' ' . $proyecto->Zip_Code . '' . $proyecto->Calle);

                    $event->getWriter()->getSheetByIndex(0)->getStyle('A' . ($row - 5) . ':A' . $row)->applyFromArray([
                        'font' => [
                            'bold' => true,
                            'size' => 11,
                        ],
                    ]);

                    //titulos header
                    $row = ($row + 1);

                    $event->getWriter()->getSheetByIndex(0)->setCellValue('A' . $row, 'Denominacion');
                    $event->getWriter()->getSheetByIndex(0)->setCellValue('B' . $row, 'Quantity pre ordered');
                    $event->getWriter()->getSheetByIndex(0)->setCellValue('C' . $row, 'Date');
                    $event->getWriter()->getSheetByIndex(0)->setCellValue('D' . $row, 'PO');
                    $event->getWriter()->getSheetByIndex(0)->setCellValue('E' . $row, 'Status');
                    $event->getWriter()->getSheetByIndex(0)->setCellValue('F' . $row, 'Quantity ordered');
                    $event->getWriter()->getSheetByIndex(0)->setCellValue('G' . $row, 'Received  Warehouse');
                    $event->getWriter()->getSheetByIndex(0)->setCellValue('H' . $row, 'Received at Projec');
                    $event->getWriter()->getSheetByIndex(0)->setCellValue('I' . $row, 'From');
                    $event->getWriter()->getSheetByIndex(0)->setCellValue('J' . $row, 'To');

                    $event->getWriter()->getSheetByIndex(0)->getStyle('A' . $row . ':J' . $row)->applyFromArray([
                        'fill' => [
                            'color' => array('rgb' => 'FF0000'),
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'color' => ['argb' => 'E1E1E1'],
                        ],
                        /* 'borders' => [
                        'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '030303'],
                        ],
                        ], */
                        'font' => [
                            'bold' => true,
                            'size' => 11,
                        ],
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                        ],

                    ]);
                    $event->getWriter()->getSheetByIndex(0)->getStyle('A' . $row . ':J' . $row)->getAlignment()->setWrapText(true);

                    $row = ($row + 1);

                    foreach ($proyecto->materiales as $key => $material) {
                        $event->getWriter()->getSheetByIndex(0)->setCellValue('A' . $row, $material->Denominacion);
                        foreach ($material->pedidos as $key => $pedido) {
                            //dd($pedido);
                            $event->getWriter()->getSheetByIndex(0)->setCellValue('B' . $row, ($pedido->pre_orden ? $pedido->pre_orden->cant_registrada : ''));
                            $event->getWriter()->getSheetByIndex(0)->setCellValue('C' . $row, $pedido->Fecha);
                            $event->getWriter()->getSheetByIndex(0)->setCellValue('D' . $row, $pedido->PO);
                            $event->getWriter()->getSheetByIndex(0)->setCellValue('E' . $row, ($pedido->pre_orden ? $pedido->pre_orden->nombre : ''));
                            $event->getWriter()->getSheetByIndex(0)->setCellValue('F' . $row, $pedido->Cantidad);
                            foreach ($pedido->movimientos as $key => $movimiento) {
                                $event->getWriter()->getSheetByIndex(0)->setCellValue('G' . $row, $movimiento->total_warehouse);
                                $event->getWriter()->getSheetByIndex(0)->setCellValue('H' . $row, $movimiento->total_proyecto);
                                $event->getWriter()->getSheetByIndex(0)->setCellValue('I' . $row, $movimiento->Pro_id_ubicacion != $pedido->Pro_ID ? $movimiento->Nombre : '');
                                $event->getWriter()->getSheetByIndex(0)->setCellValue('J' . $row, $movimiento->Pro_id_ubicacion == $pedido->Pro_ID ? $movimiento->Nombre : '');

                                $event->getWriter()->getSheetByIndex(0)->getStyle('I' . $row . ':J' . $row)->getAlignment()->setWrapText(true);
                                $row = $row + 1;
                            }
                            $row = $row + 1;
                        }
                        //dd($material);
                        $event->getWriter()->getSheetByIndex(0)->setCellValue('B' . $row, '');
                        $event->getWriter()->getSheetByIndex(0)->setCellValue('F' . $row, $material->total_cantidad);
                        $event->getWriter()->getSheetByIndex(0)->setCellValue('G' . $row, $material->total_warehouse);
                        $event->getWriter()->getSheetByIndex(0)->setCellValue('H' . $row, $material->total_proyecto);
                        $event->getWriter()->getSheetByIndex(0)->getStyle('A' . $row . ':J' . $row)->applyFromArray([
                            'font' => [
                                'bold' => false,
                                'size' => 11,
                            ],
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'color' => ['argb' => 'FEFFE6'],
                            ],
                        ]);
                        $row = $row + 1;
                    }
                    $row = $row + 1;
                }
            },
        ];
    }
}
