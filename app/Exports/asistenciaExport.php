<?php

namespace App\Exports;

use App\Personal;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Exports\PHPExcel_Style_Fill;

class asistenciaExport implements
    FromCollection,
    ShouldAutoSize,
    WithHeadings,
    WithEvents,
    WithCustomStartCell
{
    private $data;
    private $extras;
    public function __construct($data, $extras)
    {
        $this->data=$data;
        $this->extras=$extras;
    }
    use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $collection = collect($this->data);
        //dd($this->data);
        return $collection;
    }
    public function headings():array
    {
        return[
           [
                ' ',
                ' ',
                ' ',
                'Potential days to work',
                ' ',
                'Days Worked',
                ' ',
                'Off due no show up day before',
                ' ',
                'Days no show up',
                ' ',
                'Days asked be off',
                ' ',
                'Off due no work available',
                ' ',
                'Off due suspended by management',
                ' ',
                'Days work on weekend',
                ' ',
                'Days check in late',
                ' ',
            ],
           [
                'Date of hire',
                'Employee #',
                'Nick Name',
                '     #    ',
                '     %    ',
                '    #   ',
                '    %   ',
                '     #     ',
                '     %     ',
                '    #    ',
                '    %    ',
                '    #    ',
                '    %    ',
                '     #    ',
                '     %    ',
                '      #      ',
                '       %     ',
                '    #    ',
                '    %    ',
                '    #    ',
                '    %    '
            ],
        ];
    }
    public function registerEvents():array
    {
        return[
            AfterSheet::class=>function (AfterSheet $event) {
                //subtitulos
                $event->sheet->getStyle('D4:U4')->applyFromArray([
                    'fill'=>[
                        'color' => array('rgb' => 'FF0000')
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
                $event->sheet->getStyle('A5:U5')->applyFromArray([
                    'fill'=>[
                        'color' => array('rgb' => 'FF0000')
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
                //titulo
                $event->sheet->getStyle('C3:U3')->applyFromArray([
                    'font'=>[
                        'bold'=>true,
                        'size'=>12,
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
                //celda bajo porcentaje recorrer todo
                for ($i=0; $i < $this->extras->total_registros ; $i++) {
                    //100%
                    $event->sheet->getStyle('E'.(6+$i))
                    ->getNumberFormat()
                    ->setFormatCode(
                        \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE
                    );
                    /* poscentajes */
                    //Days Worked
                    //formula porcentaje
                    $event->sheet->setCellValue('G'.(6+$i), '=+F'.(6+$i).'/D'.(6+$i).'');
                    //tipo de celda porcentaje
                    $event->sheet->getStyle('G'.(6+$i))
                    ->getNumberFormat()
                    ->setFormatCode(
                        \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE
                    );
                    //Off due no show up day before
                    $event->sheet->setCellValue('I'.(6+$i), '=+H'.(6+$i).'/D'.(6+$i).'');
                    
                    $event->sheet->getStyle('I'.(6+$i))
                    ->getNumberFormat()
                    ->setFormatCode(
                        \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE
                    );
                    //Days no show up
                    $event->sheet->setCellValue('K'.(6+$i), '=+J'.(6+$i).'/D'.(6+$i).'');

                    $event->sheet->getStyle('K'.(6+$i))
                    ->getNumberFormat()
                    ->setFormatCode(
                        \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE
                    );
                    //Days asked be off
                    $event->sheet->setCellValue('M'.(6+$i), '=+L'.(6+$i).'/D'.(6+$i).'');

                    $event->sheet->getStyle('M'.(6+$i))
                    ->getNumberFormat()
                    ->setFormatCode(
                        \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE
                    );

                    //Off due no work available
                    $event->sheet->setCellValue('O'.(6+$i), '=+N'.(6+$i).'/D'.(6+$i).'');

                    $event->sheet->getStyle('O'.(6+$i))
                    ->getNumberFormat()
                    ->setFormatCode(
                        \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE
                    );
                    //Off due suspended by management
                    $event->sheet->setCellValue('Q'.(6+$i), '=+P'.(6+$i).'/D'.(6+$i).'');

                    $event->sheet->getStyle('Q'.(6+$i))
                    ->getNumberFormat()
                    ->setFormatCode(
                        \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE
                    );
                    //Days work on weekend
                    $event->sheet->setCellValue('S'.(6+$i), '=+R'.(6+$i).'/D'.(6+$i).'');

                    $event->sheet->getStyle('S'.(6+$i))
                    ->getNumberFormat()
                    ->setFormatCode(
                        \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE
                    );
                    //Days check in late
                    $event->sheet->setCellValue('U'.(6+$i), '=+T'.(6+$i).'/F'.(6+$i).'');

                    $event->sheet->getStyle('U'.(6+$i))
                    ->getNumberFormat()
                    ->setFormatCode(
                        \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE
                    );

                    //stylos a todo el resultado center
                    $event->sheet->getStyle('A'.(6+$i).':U'.(6+$i))->applyFromArray([
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        ],
                    ]);
                }
               
                //$event->sheet->setCellValue('A3', 'Date of hire');
                //$event->sheet->setCellValue('B3', 'Employee #');
                //$event->sheet->setCellValue('C3', 'Nick Name');
                $event->sheet->mergeCells('D4:E4');
                //$event->sheet->setCellValue('D2', 'Potential days to work');
                //$event->sheet->setCellValue('D3', '#');
                //$event->sheet->setCellValue('E3', '%');
                $event->sheet->mergeCells('F4:G4');
                //$event->sheet->setCellValue('F2', 'Days Worked');
                //$event->sheet->setCellValue('F3', '#');
                //$event->sheet->setCellValue('G3', '%');
                $event->sheet->mergeCells('H4:I4');
                //$event->sheet->setCellValue('H2', 'Off due no show up day before');
                //$event->sheet->setCellValue('H3', '#');
                //$event->sheet->setCellValue('I3', '%');
                $event->sheet->mergeCells('J4:K4');
                //$event->sheet->setCellValue('J2', 'Days no show up');
                //$event->sheet->setCellValue('J3', '#');
                //$event->sheet->setCellValue('K3', '%');
                $event->sheet->mergeCells('L4:M4');
                //$event->sheet->setCellValue('L2', 'Days asked be off');
                //$event->sheet->setCellValue('L3', '#');
                //$event->sheet->setCellValue('M3', '%');
                $event->sheet->mergeCells('N4:O4');
                //$event->sheet->setCellValue('N2', 'Off due no work available');
                //$event->sheet->setCellValue('N3', '#');
                //$event->sheet->setCellValue('O3', '%');
                $event->sheet->mergeCells('P4:Q4');
                $event->sheet->mergeCells('R4:S4');
                $event->sheet->mergeCells('T4:U4');
                //$event->sheet->setCellValue('P2', 'Off due suspended by management');
                //$event->sheet->setCellValue('P3', '#');
                //$event->sheet->setCellValue('Q3', '%');*/

                ////calculos
                //$event->sheet->setCellValue('G4', '=+D4/F4');
                
                
                //$event->sheet->setCellValue('E4', '=SUM(D4:G4)');
                
                ///titulos
                $event->sheet->setCellValue('C3', 'Report of Attendance from '.$this->extras->fecha_inicio.' to '.$this->extras->fecha_fin);
                $event->sheet->setCellValue('A1', 'TOTAL RECORDS  '.$this->extras->total_registros);
                $event->sheet->setCellValue('A2', $this->extras->empresa->Nombre);
                $event->sheet->mergeCells('C3:U3');
            }
        ];
    }
  
    public function startCell():string
    {
        return 'A4';
    }
}
