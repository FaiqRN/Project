<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DokumentasiSeeder extends Seeder
{
   public function run(): void
   {
       // Dokumentasi untuk agenda Kegiatan 1 (Seminar AI)
       $data = [
           [
               'dokumentasi_id' => 1,
               'nama_dokumentasi' => 'Dokumentasi Registrasi Seminar AI',
               'file_dokumentasi' => 'dok_registrasi_ai_seminar.pdf',
               'tanggal' => '2024-01-15',
               'created_at' => Carbon::now(),
               'updated_at' => Carbon::now()
           ],
           [
               'dokumentasi_id' => 2,
               'nama_dokumentasi' => 'Dokumentasi Keynote Speaker Seminar AI',
               'file_dokumentasi' => 'dok_keynote_ai_seminar.pdf',
               'tanggal' => '2024-01-15',
               'created_at' => Carbon::now(),
               'updated_at' => Carbon::now()
           ],
           [
               'dokumentasi_id' => 3,
               'nama_dokumentasi' => 'Dokumentasi Panel Discussion AI',
               'file_dokumentasi' => 'dok_panel_ai_seminar.pdf',
               'tanggal' => '2024-01-15',
               'created_at' => Carbon::now(),
               'updated_at' => Carbon::now()
           ],
           [
               'dokumentasi_id' => 4,
               'nama_dokumentasi' => 'Dokumentasi Workshop AI Implementation',
               'file_dokumentasi' => 'dok_workshop_ai_seminar.pdf',
               'tanggal' => '2024-01-16',
               'created_at' => Carbon::now(),
               'updated_at' => Carbon::now()
           ],
           [
               'dokumentasi_id' => 5,
               'nama_dokumentasi' => 'Dokumentasi Closing Seminar AI',
               'file_dokumentasi' => 'dok_closing_ai_seminar.pdf',
               'tanggal' => '2024-01-16',
               'created_at' => Carbon::now(),
               'updated_at' => Carbon::now()
           ],
       

           [
               'dokumentasi_id' => 6,
               'nama_dokumentasi' => 'Dokumentasi Introduction Data Science',
               'file_dokumentasi' => 'dok_intro_ds_workshop.pdf',
               'tanggal' => '2024-03-01',
               'created_at' => Carbon::now(),
               'updated_at' => Carbon::now()
           ],
           [
               'dokumentasi_id' => 7,
               'nama_dokumentasi' => 'Dokumentasi Hands-on Data Analysis',
               'file_dokumentasi' => 'dok_handson_ds_workshop.pdf',
               'tanggal' => '2024-03-02',
               'created_at' => Carbon::now(),
               'updated_at' => Carbon::now()
           ],
           [
               'dokumentasi_id' => 8,
               'nama_dokumentasi' => 'Dokumentasi Final Project Data Science',
               'file_dokumentasi' => 'dok_final_ds_workshop.pdf',
               'tanggal' => '2024-03-03',
               'created_at' => Carbon::now(),
               'updated_at' => Carbon::now()
           ]
       ];


       // Insert ke database
       DB::table('m_dokumentasi')->insert($data);
   }
}