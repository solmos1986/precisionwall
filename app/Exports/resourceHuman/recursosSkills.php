<?php

namespace App\Exports\resourceHuman;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class recursosSkills implements
FromCollection,
ShouldAutoSize,
WithHeadings,
WithEvents,
WithCustomStartCell,
WithColumnFormatting
{
    private $data;
    private $extras;
    public function __construct($data)
    {
        $this->data = $data;
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
            '#',
            'Num.',
            'NickName',
            'Postion',
            'Email',
            'Skill'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->setCellValue('A1', 'Report employees by skills Excel ' . date('m-d-Y'));

                $event->sheet->mergeCells('A1:G1');
                $event->sheet->getColumnDimension('A')->setAutoSize(false);
                $event->sheet->getColumnDimension('B')->setAutoSize(false);
                $event->sheet->getColumnDimension('E')->setAutoSize(false);
                $event->sheet->getColumnDimension('I')->setAutoSize(false);
                $event->sheet->getColumnDimension('H')->setAutoSize(false);
                $event->sheet->getColumnDimension('G')->setAutoSize(false);
                $event->sheet->getColumnDimension('J')->setAutoSize(false);
                $event->sheet->getColumnDimension('F')->setAutoSize(false);

                $event->sheet->getColumnDimension('A')->setWidth(5);
                $event->sheet->getColumnDimension('B')->setWidth(10);
                $event->sheet->getColumnDimension('E')->setWidth(30);
                $event->sheet->getColumnDimension('F')->setWidth(90);
                $event->sheet->getColumnDimension('G')->setWidth(16);
                $event->sheet->getColumnDimension('H')->setWidth(15);
               

                $event->sheet->getStyle('A2:F100')->applyFromArray([
                    'alignment' => [
                        'wrapText' => true,
                    ],
                ]);
                $event->sheet->getStyle('A1:F1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 12,
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                ]);
                $event->sheet->getStyle('A2:F2')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['rgb' => '030303'],
                        ],
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'wrapText' => true,
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
