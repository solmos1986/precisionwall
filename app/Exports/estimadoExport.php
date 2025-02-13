<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class estimadoExport implements
FromCollection,
ShouldAutoSize,
WithHeadings,
WithEvents,
WithCustomStartCell,
WithColumnFormatting
{
    private $data;
    private $proyecto;
    private $extras;
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
            [
                'Cod Area',
                'Area Description',
                'Cost Code',
                'CC Description',
                'Budget QTY',
                'UM',
                '# OF COATS',
                'PWT PROD RATE',
                'ESTIMATED HOURS',
                'ESTIMATED LABOR COST',
                'MATERIAL or EQUIPMENT UNIT COST',
                'MATERIAL SPREAD RATE PER UNIT',
                'MAT QTY or GALLONS / UNIT',
                'MAT UM', 'MATERIAL COST',
                'PRICE',
                'SUBCONTRACT COST',
                'EQUIPMENT COST',
                'OTHER COST',
            ],
        ];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->setCellValue('A1', $this->proyecto->Codigo);
                $event->sheet->setCellValue('B1', $this->proyecto->Nombre);
                $event->sheet->mergeCells('B1:S1');
                $event->sheet->getStyle('A1:S1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 12,
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                ]);
                $event->sheet->getStyle('A2:S2')->applyFromArray([
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
            //'C' => NumberFormat::FORMAT_TEXT,
        ];
    }
    public function startCell(): string
    {
        return 'A2';
    }
}
