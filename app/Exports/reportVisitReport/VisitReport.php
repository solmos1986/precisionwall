<?php

namespace App\Exports\reportVisitReport;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class VisitReport implements
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
            'General Contractor',
            'Code',
            'Project',
            'Nro. VR',
            'Date Report',
            'Report By',
            'Emailed',
            'Downloaded',
            'Coments',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->setCellValue('A1', 'Field Visit Reports ' . date('m-d-Y'));

                $event->sheet->mergeCells('A1:G1');

                $event->sheet->getColumnDimension('I')->setAutoSize(false);
                $event->sheet->getColumnDimension('H')->setAutoSize(false);
                $event->sheet->getColumnDimension('G')->setAutoSize(false);
                $event->sheet->getColumnDimension('J')->setAutoSize(false);

                $event->sheet->getColumnDimension('G')->setWidth(16);
                $event->sheet->getColumnDimension('H')->setWidth(15);
                $event->sheet->getColumnDimension('E')->setWidth(15);
                $event->sheet->getColumnDimension('I')->setWidth(100);
                $event->sheet->getColumnDimension('J')->setWidth(15);

                $event->sheet->getStyle('A2:I12')->applyFromArray([
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
                $event->sheet->getStyle('A2:I2')->applyFromArray([
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
            //'G' => NumberFormat::FORMAT_PERCENTAGE,
        ];
    }
    public function startCell(): string
    {
        return 'A2';
    }
}
