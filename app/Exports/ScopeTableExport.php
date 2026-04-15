<?php

namespace App\Exports;

use Illuminate\Container\Attributes\Log;
use Illuminate\Support\Facades\Log as FacadesLog;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;

class ScopeTableExport implements
    FromArray,
    WithHeadings,
    WithStyles,
    WithEvents,
    ShouldAutoSize
{
    protected $rows;
    protected $headings;
    protected $title;

    public function __construct($rows, $headings, $title)
    {
        $this->rows = $rows;
        $this->headings = $headings;
        $this->title = $title;
    }

    public function array(): array
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function styles(Worksheet $sheet)
    {
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                $columnCount = count($this->headings);
                $lastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnCount);

                $data = $sheet->toArray();

                $sheet->removeRow(1, count($data));

                $sheet->fromArray($data, null, 'A4');

                // 🎯 TITLE (row 1)
                $sheet->mergeCells("A1:{$lastColumn}1");
                $sheet->setCellValue('A1', $this->title);

                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                    ],
                    'alignment' => [
                        'horizontal' => 'left',
                        'vertical' => 'center',
                    ],
                ]);

                // 🎯 HEADER (row 4)
                $sheet->getStyle("A4:{$lastColumn}4")->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'alignment' => [
                        'horizontal' => 'center',
                        'vertical' => 'center',
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => 'medium',
                        ],
                    ],
                ]);

                // 🎯 DATA (row 5+)
                $rowCount = count($this->rows) + 4;

                $sheet->getStyle("A4:{$lastColumn}{$rowCount}")
                    ->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => 'thin',
                            ],
                        ],
                        'alignment' => [
                            'vertical' => 'center',
                            'wrapText' => true,
                        ],
                    ]);

                $sheet->getStyle("A5:A{$rowCount}")
                    ->getAlignment()->setHorizontal('center');

                $sheet->getStyle("D5:D{$rowCount}")
                    ->getAlignment()->setHorizontal('right');
            },
        ];
    }
}
