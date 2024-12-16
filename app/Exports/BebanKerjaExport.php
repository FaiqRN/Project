<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BebanKerjaExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $data;
    protected $chartData;

    public function __construct($data, $chartData)
    {
        $this->data = $data;
        $this->chartData = $chartData;
    }

    public function collection()
    {
        return collect($this->data)->map(function ($item) {
            return [
                'nama_dosen' => $item->nama_dosen,
                'nama_kegiatan' => $item->nama_kegiatan,
                'jenis_kegiatan' => $item->jenis_kegiatan,
                'tanggal' => $item->tanggal,
                'status' => 'Selesai',
                'poin_jti' => $item->poin_jti,
                'poin_non_jti' => $item->poin_non_jti,
                'total_poin' => $item->total_poin
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Nama Dosen',
            'Nama Kegiatan',
            'Jenis Kegiatan',
            'Tanggal',
            'Status',
            'Poin JTI',
            'Poin Non-JTI',
            'Total'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}