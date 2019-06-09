<?php

namespace App\Exports;

use App\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class DataExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
	private $data;

    public function __construct($data, $head)
    {
        $this->data = $data;
        $this->head = $head;
    }

    /**
    * @return \Illuminate\Support\Collection
    */

    public function collection()
    {
    	return collect($this->data);
    }
    public function headings(): array
    {
        return $this->head;
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRange = 'A1:AH1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
            },
        ];
    }
}

