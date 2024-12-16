<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BebanKerjaExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data)->map(function($item) {
            return [
                'nama_dosen' => $item->nama_dosen,
                'nama_kegiatan' => $item->nama_kegiatan,
                'jenis_kegiatan' => $item->jenis_kegiatan,
                'tanggal' => $item->tanggal,
                'status' => 'Selesai',
                'poin_jti' => round($item->poin_jti),
                'poin_non_jti' => round($item->poin_non_jti),
                'total_poin' => round($item->total_poin)
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
            'Total Keseluruhan'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'A1:H1' => ['fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E2EFDA']
            ]],
        ];
    }
}