<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Excel;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class orden_proyecto_total_Export implements
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
                $event->writer->reopen(new \Maatwebsite\Excel\Files\LocalTemporaryFile(public_path() . '/plantilla/' . 'report_materiales.xlsx'), Excel::XLSX);
                $event->writer->getSheetByIndex(0);
                // fill with information
                $event->getWriter()->getSheetByIndex(0)->setCellValue('A2', "Material and Equipment Report " . date('Y/m/d', strtotime($this->fecha_inicio)) . " to " . date('Y/m/d', strtotime($this->fecha_fin)));
                $row = 4;
                //dd($this->proyectos);
                foreach ($this->proyectos as $i => $proyecto) {
                    //titulos proyectos
                    $event->getWriter()->getSheetByIndex(0)->setCellValue('A' . $row, 'GENERAL CONTRACTOR');
                    $event->getWriter()->getSheetByIndex(0)->mergeCells('B' . $row . ':D' . $row);
                    $event->getWriter()->getSheetByIndex(0)->setCellValue('B' . $row, $proyecto->nombre_empresa);

                    $row = ($row + 1);
                    $event->getWriter()->getSheetByIndex(0)->setCellValue('A' . $row, 'PRECISION WALL TECH PROJECT');
                    $event->getWriter()->getSheetByIndex(0)->mergeCells('B' . $row . ':H' . $row);
                    $event->getWriter()->getSheetByIndex(0)->setCellValue('B' . $row, ($proyecto->Codigo . ' / ' . $proyecto->Nombre . ' / ' . $proyecto->nombre_estatus . ' / ' . $proyecto->Ciudad . ' ' . $proyecto->Zip_Code . ' ' . $proyecto->Calle . ' ' . $proyecto->Estado));

                    /*  $row = ($row + 1);
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
                    $event->getWriter()->getSheetByIndex(0)->setCellValue('B' . $row, $proyecto->Estado . ' ' . $proyecto->Estado . ' ' . $proyecto->Zip_Code . '' . $proyecto->Calle); */

                    $event->getWriter()->getSheetByIndex(0)->getStyle('A' . ($row - 5) . ':A' . $row)->applyFromArray([
                        'font' => [
                            'bold' => true,
                            'size' => 11,
                        ],
                    ]);

                    //titulos header
                    $row = ($row + 1);

                    $event->getWriter()->getSheetByIndex(0)->setCellValue('A' . $row, 'Denominacion');
                    $event->getWriter()->getSheetByIndex(0)->setCellValue('B' . $row, 'Unit of measure');
                    $event->getWriter()->getSheetByIndex(0)->setCellValue('C' . $row, 'Type material');
                    $event->getWriter()->getSheetByIndex(0)->setCellValue('D' . $row, 'Received Warehouse');
                    $event->getWriter()->getSheetByIndex(0)->setCellValue('E' . $row, 'Received at Project');
                    $event->getWriter()->getSheetByIndex(0)->setCellValue('F' . $row, 'Projects');

                    $event->getWriter()->getSheetByIndex(0)->getStyle('A' . $row . ':H' . $row)->applyFromArray([
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
                    //dd($this->proyectos[0]);
                    foreach ($proyecto->materiales as $key => $material) {
                        //dd($material);
                        foreach ($material->proyectos_total as $key => $proyecto_total) {
                            /* $event->getWriter()->getSheetByIndex(0)->setCellValue('B' . $row, $material->Denominacion);
                            $event->getWriter()->getSheetByIndex(0)->setCellValue('C' . $row, $material->Unidad_Medida);
                            $event->getWriter()->getSheetByIndex(0)->setCellValue('D' . $row, $material->Nombre); */
                            $event->getWriter()->getSheetByIndex(0)->setCellValue('E' . $row, $proyecto_total->por_proyecto);
                            $event->getWriter()->getSheetByIndex(0)->setCellValue('F' . $row, $proyecto_total->nombre_proyecto);
                            /*       foreach ($pedido->movimientos as $key => $movimiento) {
                            $event->getWriter()->getSheetByIndex(0)->setCellValue('G' . $row, $movimiento->enWarehouse);
                            $event->getWriter()->getSheetByIndex(0)->setCellValue('H' . $row, $movimiento->enProyecto);
                            $event->getWriter()->getSheetByIndex(0)->setCellValue('I' . $row, $movimiento->enWarehouseNombre);
                            $event->getWriter()->getSheetByIndex(0)->setCellValue('J' . $row, $movimiento->enProyectoNombre);

                            $event->getWriter()->getSheetByIndex(0)->getStyle('I' . $row . ':J' . $row)->getAlignment()->setWrapText(true);
                            $row = $row + 1;
                            } */
                            $row = $row + 1;
                        }
                        //dd($material);
                        $event->getWriter()->getSheetByIndex(0)->setCellValue('A' . $row, $material->Denominacion);
                        $event->getWriter()->getSheetByIndex(0)->setCellValue('B' . $row, $material->Unidad_Medida);
                        $event->getWriter()->getSheetByIndex(0)->setCellValue('C' . $row, $material->Nombre);
                        $event->getWriter()->getSheetByIndex(0)->setCellValue('D' . $row, $material->total_warehouse);
                        $event->getWriter()->getSheetByIndex(0)->setCellValue('E' . $row, $material->total_proyecto);
                        $event->getWriter()->getSheetByIndex(0)->getStyle('A' . $row . ':H' . $row)->applyFromArray([
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
