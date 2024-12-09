<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SuratSeeder extends Seeder{

   public function run(): void{
    
    $data = [
        [
            'surat_id' => 1,
            'nomer_surat' => '2089/PL2.1/KP/2024',
            'judul_surat' => 'Kegiatan Workshop Pengembangan Kurikulum Jurusan',
            'file_surat' => 'SURAT PENUGASAN JURUSAN.pdf',
            'tanggal_surat' => '2024-12-05',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'surat_id' => 2,
            'nomer_surat' => '2090/PL2.1/KP/2024',
            'judul_surat' => 'Kegiatan Evaluasi Program Studi',
            'file_surat' => 'SURAT PENUGASAN PROGRAM STUDI.pdf',
            'tanggal_surat' => '2024-12-15',
            'created_at' => now(),
            'updated_at' => now()
        ],
    ];

       DB::table('m_surat')->insert($data);
   }
}