<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Excel;

class orden_material_total_detalle_Export implements
WithEvents
{
    private $proyectos;
    private $extras;
    private $fecha_inicio;
    private $fecha_fin;
    public function __construct($proyectos, $extras, $fecha_inicio, $fecha_fin)
    {
        $this->data = $proyectos;
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
                //dd($this->proyectos);
                // fill with information
                $event->getWriter()->getSheetByIndex(0)->setCellValue('A1', "Material and Equipment Report " . date('Y-m-d', strtotime($fecha_inicio)) . " to " . date('Y-m-d', strtotime($fecha_fin)));
                $row = 10;
                foreach ($this->proyectos as $i => $proyecto) {
                    foreach ($proyecto->materiales as $key => $material) {
                        $event->getWriter()->getSheetByIndex(0)->setCellValue('A' . $row, $material->Denominacion);
                        foreach ($material->pedidos as $key => $pedido) {
                            foreach ($pedido->movimientos as $key => $movimiento) {
                                
                            }
                        }
                        $row++;
                    }
                    
                    /* if ($proyecto->nombre_proyecto == 'Total') {
                        $event->getWriter()->getSheetByIndex(0)->getStyle('A' . $row . ':I' . $row . '')->applyFromArray([
                            'font' => [
                                'size' => 11,
                                'bold' => false,
                                'color' => ['argb' => '000FFF'],
                            ],
                            'fill' => [
                                'color' => ['argb' => 'EB2B02'],
                            ],
                        ]);
                        $row++;
                    }
                    $row++; */
                }
            },
        ];
    }

    public function startCell(): string
    {
        return 'A2';
    }
}
