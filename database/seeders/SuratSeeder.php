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
            'nomer_surat' => '001/PAN/TI/2024',
            'judul_surat' => 'Undangan Seminar AI',
            'file_surat' => 'surat_seminar_ai.pdf',
            'tanggal_surat' => '2024-01-10',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'surat_id' => 2,
            'nomer_surat' => '002/PAN/TI/2024',
            'judul_surat' => 'Undangan Workshop Data Science',
            'file_surat' => 'surat_workshop_ds.pdf',
            'tanggal_surat' => '2024-02-15',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'surat_id' => 3,
            'nomer_surat' => '003/PAN/TI/2024',
            'judul_surat' => 'Undangan Konferensi Internasional',
            'file_surat' => 'surat_conference.pdf',
            'tanggal_surat' => '2024-03-20',
            'created_at' => now(),
            'updated_at' => now()
        ]
    ];

       DB::table('m_surat')->insert($data);
   }
}