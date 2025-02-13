<?php

namespace App\Exports\reportsAvances;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class estimadoExportEdificioSov implements
FromCollection,
ShouldAutoSize,
WithHeadings,
WithEvents,
WithCustomStartCell,
WithColumnFormatting
{
    private $data;
    private $extras;
    private $proyecto;
    public function __construct($data, $proyecto)
    {
        $this->data = $data;
        $this->proyecto = $proyecto;
    }
    use Exportable;
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $collection = collect($this->data);
        return $collection;
    }
    public function headings(): array
    {
        return [
            'Building',
            'Floor',
            'Cod Area',
            'Area',
            'SOV Code',
            'SOV Task',
            'Price',
            '% Completed',
            '% Date Las Record',
            'To Bill Acording % Completed',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->setCellValue('A1', $this->proyecto->Codigo);
                $event->sheet->setCellValue('B1', $this->proyecto->Nombre);
                $event->sheet->mergeCells('B1:J1');
                //size
                $event->sheet->getColumnDimension('I')->setAutoSize(false);
                $event->sheet->getColumnDimension('H')->setAutoSize(false);
                $event->sheet->getColumnDimension('G')->setAutoSize(false);
                $event->sheet->getColumnDimension('J')->setAutoSize(false);

                $event->sheet->getColumnDimension('G')->setWidth(16);
                $event->sheet->getColumnDimension('H')->setWidth(15);
                $event->sheet->getColumnDimension('E')->setWidth(15);
                $event->sheet->getColumnDimension('I')->setWidth(20);
                $event->sheet->getColumnDimension('J')->setWidth(15);

                $event->sheet->getStyle('A2:J2')->applyFromArray([
                    'alignment' => [
                        'wrapText' => true,
                    ],
                ]);
                $event->sheet->getStyle('A1:I1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 12,
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                ]);
                $event->sheet->getStyle('A2:j2')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['rgb' => '030303'],
                        ],
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                    'fill' => [
                        'color' => array('rgb' => 'd6d6d6'),
                    ],
                ]);
            },
        ];
    }
    public function columnFormats(): array
    {
        return [
            'H' => NumberFormat::FORMAT_PERCENTAGE,
            //'C' => NumberFormat::FORMAT_NUMBER,
        ];
    }
    public function startCell(): string
    {
        return 'A2';
    }
}
