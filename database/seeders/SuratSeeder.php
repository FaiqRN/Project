<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SuratSeeder extends Seeder
{
   /**
    * Run the database seeds.
    */
   public function run(): void
   {
       $data = [
           [
               'surat_id' => 1,
               'nomer_surat' => '001/JTI/POLIJE/2024',
               'judul_surat' => 'Surat Tugas Mengikuti Seminar Nasional Artificial Intelligence',
               'file_surat' => 'surat_tugas_seminar_ai.pdf',
               'tanggal_surat' => '2024-01-10', // 5 hari sebelum kegiatan dimulai
               'created_at' => Carbon::now(),
               'updated_at' => Carbon::now()
           ],
           [
               'surat_id' => 2,
               'nomer_surat' => '002/JTI/POLIJE/2024',
               'judul_surat' => 'Surat Tugas Workshop Data Science for Industry 4.0',
               'file_surat' => 'surat_tugas_workshop_ds.pdf',
               'tanggal_surat' => '2024-02-25', // sekitar 5 hari sebelum kegiatan dimulai
               'created_at' => Carbon::now(),
               'updated_at' => Carbon::now()
           ]
       ];

       DB::table('m_surat')->insert($data);
   }
}