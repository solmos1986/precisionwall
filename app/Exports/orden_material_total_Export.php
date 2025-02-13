<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class orden_material_total_Export implements
ShouldAutoSize,
WithHeadings,
WithEvents
{
    private $data;
    private $extras;
    private $fecha_inicio;
    private $fecha_fin;
    public function __construct($data, $extras, $fecha_inicio, $fecha_fin)
    {
        $this->data = $data;
        $this->extras = $extras;
        $this->fecha_inicio = $fecha_inicio;
        $this->fecha_fin = $fecha_fin;
    }
    use Exportable;
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $resultado_materiales = [];
        $resultado_proyectos = [];
        $resultado_movimientos = [];

        foreach ($this->data as $key => $material) {
            $materiales = new \stdClass();
            $materiales->Material_Equipament = $material->Denominacion;
            $materiales->Unit = $material->Unidad_Medida;
            foreach ($material->proyectos as $key => $proyecto) {
                $proyectos = new \stdClass();
                $proyectos->ubicacion_proyecto = $proyecto->ubicacion_proyecto;
                $proyectos->codigo = $proyecto->codigo;
                foreach ($proyecto->movimientos as $key => $items) {
                    $movimientos = new \stdClass();
                    $movimientos->fecha = $items->fecha;
                    $movimientos->nombre_status = $items->nombre_status;
                    $movimientos->ingreso = $items->ingreso;
                    $movimientos->egreso = $items->egreso;
                    $movimientos->from = $items->nombre_vendor;
                    $resultado_movimientos[] = $movimientos;
                }
                $resultado_proyectos[] = $proyectos;
            }
            $resultado_materiales[] = $materiales;
        }
        return collect($resultado_movimientos)->each(function ($value) {
            return $value;
        });
    }

    public function headings(): array
    {
        return [

        ];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                //$event->sheet->getDelegate()->getRowDimension('7')->setRowHeight(40);
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(50);
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(50);
                //subtitulos
                /*  $event->sheet->getStyle('D4:U4')->applyFromArray([
                'fill' => [
                'color' => array('rgb' => 'FF0000'),
                ],
                'borders' => [
                'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => ['rgb' => '030303'],
                ],
                ],
                'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
                ]); */
                /*  $event->sheet->getStyle('A5:U5')->applyFromArray([
                'fill' => [
                'color' => array('rgb' => 'FF0000'),
                ],
                'borders' => [
                'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => ['rgb' => '030303'],
                ],
                ],
                'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
                ]); */
                //titulo
                $event->sheet->getStyle('A3:E3')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 12,
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                ]);
                //fecha
                $event->sheet->getStyle('A6')
                    ->getNumberFormat()
                    ->setFormatCode(
                        \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_XLSX14
                    );
                $event->sheet->getStyle('B6')
                    ->getNumberFormat()
                    ->setFormatCode(
                        \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_XLSX14
                    );
                $proyectos = $this->data;
                $posision = 0;
                //celda bajo porcentaje recorrer todo
                for ($i = 0; $i < count($proyectos); $i++) {

                    $event->sheet->setCellValue('A' . (4 + $posision), 'Material:');
                    $event->sheet->setCellValue('B' . (4 + $posision), $proyectos[$i]->Denominacion);
                    $event->sheet->setCellValue('C' . (4 + $posision), $proyectos[$i]->Nombre);

                    //titles
                    $event->sheet->setCellValue('A' . (5 + $posision), 'Projects');
                    $event->sheet->setCellValue('B' . (5 + $posision), 'Cod');
                    $event->sheet->setCellValue('C' . (5 + $posision), 'Quatity');
                    $event->sheet->getStyle('A' . (5 + $posision) . ':C' . (5 + $posision))->applyFromArray([
                        'fill' => [
                            'color' => array('rgb' => 'FF0000'),
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['rgb' => '030303'],
                            ],
                        ],
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        ],
                    ]);
                    foreach ($proyectos[$i]->proyectos as $j => $proyecto) {
                        $event->sheet->setCellValue('A' . (6 + $posision + $j), $proyecto->Nombre);
                        $event->sheet->setCellValue('B' . (6 + $posision + $j), $proyecto->Codigo);
                        $event->sheet->setCellValue('C' . (6 + $posision + $j), $proyecto->total);
                        /* $event->sheet->setCellValue('A' . (7 + $j), 'Date');
                    $event->sheet->setCellValue('B' . (7 + $j), 'Status');
                    $event->sheet->setCellValue('C' . (7 + $j), 'Income');
                    $event->sheet->setCellValue('D' . (7 + $j), 'Output');
                    $event->sheet->setCellValue('E' . (7 + $j), 'From');

                    foreach ($proyecto->movimientos as $K => $items) {

                    $event->sheet->setCellValue('A' . (8 + $K), $items->fecha);
                    $event->sheet->setCellValue('B' . (8 + $K), $items->nombre_status);
                    $event->sheet->setCellValue('C' . (8 + $K), $items->ingreso);
                    $event->sheet->setCellValue('D' . (8 + $K), $items->egreso);
                    $event->sheet->setCellValue('E' . (8 + $K), $items->nombre_vendor);

                    } */

                    }
                    $posision = $i + count($proyectos[$i]->proyectos) + 4;

                }

                $event->sheet->setCellValue('A3', 'Order Report ' . $this->fecha_inicio . '  ' . $this->fecha_fin);
                $event->sheet->mergeCells('A3:E3');

                $event->sheet->setCellValue('A1', 'TOTAL MATERIAL ');
                $event->sheet->setCellValue('A2', 'Precision Wall Tech Inc.');
            },
        ];
    }

}
