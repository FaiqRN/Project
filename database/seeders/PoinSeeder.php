<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PoinSeeder extends Seeder
{
   public function run(): void
   {
       // Poin untuk agenda Kegiatan 1 (Seminar AI)
       $data = [
           [
               'poin_id' => 1,
               'user_id' => 3,
               'agenda_id' => 1,
               'kegiatan_id' => 1,
               'jumlah_poin' => '5',
               'nidn' => '1122334455',
               'nama_lengkap' => 'Dosen Pengajar Satu',
               'nama_kegiatan' => 'Seminar Nasional Artificial Intelligence',
               'nama_agenda' => 'Registrasi dan Pembukaan',
               'tanggal_agenda' => '2024-01-15',
               'created_at' => Carbon::now(),
               'updated_at' => Carbon::now()
           ],
           [
               'poin_id' => 2,
               'user_id' => 3,
               'agenda_id' => 2,
               'kegiatan_id' => 1,
               'jumlah_poin' => '5',
               'nidn' => '1122334455',
               'nama_lengkap' => 'Dosen Pengajar Satu',
               'nama_kegiatan' => 'Seminar Nasional Artificial Intelligence',
               'nama_agenda' => 'Keynote Speaker Session',
               'tanggal_agenda' => '2024-01-15',
               'created_at' => Carbon::now(),
               'updated_at' => Carbon::now()
           ],
           [
               'poin_id' => 3,
               'user_id' => 3,
               'agenda_id' => 3,
               'kegiatan_id' => 1,
               'jumlah_poin' => '5',
               'nidn' => '1122334455',
               'nama_lengkap' => 'Dosen Pengajar Satu',
               'nama_kegiatan' => 'Seminar Nasional Artificial Intelligence',
               'nama_agenda' => 'Panel Discussion: AI in Industry',
               'tanggal_agenda' => '2024-01-15',
               'created_at' => Carbon::now(),
               'updated_at' => Carbon::now()
           ],
           [
               'poin_id' => 4,
               'user_id' => 3,
               'agenda_id' => 4,
               'kegiatan_id' => 1,
               'jumlah_poin' => '5',
               'nidn' => '1122334455',
               'nama_lengkap' => 'Dosen Pengajar Satu',
               'nama_kegiatan' => 'Seminar Nasional Artificial Intelligence',
               'nama_agenda' => 'Workshop AI Implementation',
               'tanggal_agenda' => '2024-01-16',
               'created_at' => Carbon::now(),
               'updated_at' => Carbon::now()
           ],
           [
               'poin_id' => 5,
               'user_id' => 3,
               'agenda_id' => 5,
               'kegiatan_id' => 1,
               'jumlah_poin' => '5',
               'nidn' => '1122334455',
               'nama_lengkap' => 'Dosen Pengajar Satu',
               'nama_kegiatan' => 'Seminar Nasional Artificial Intelligence',
               'nama_agenda' => 'Closing dan Networking',
               'tanggal_agenda' => '2024-01-16',
               'created_at' => Carbon::now(),
               'updated_at' => Carbon::now()
           ],
     

       // Poin untuk agenda Kegiatan 2 (Workshop Data Science)
           [
               'poin_id' => 6,
               'user_id' => 4,
               'agenda_id' => 6,
               'kegiatan_id' => 2,
               'jumlah_poin' => '5',
               'nidn' => '2233445566',
               'nama_lengkap' => 'Dosen Pengajar Dua',
               'nama_kegiatan' => 'Workshop Data Science for Industry 4.0',
               'nama_agenda' => 'Introduction to Data Science',
               'tanggal_agenda' => '2024-03-01',
               'created_at' => Carbon::now(),
               'updated_at' => Carbon::now()
           ],
           [
               'poin_id' => 7,
               'user_id' => 4,
               'agenda_id' => 7,
               'kegiatan_id' => 2,
               'jumlah_poin' => '5',
               'nidn' => '2233445566',
               'nama_lengkap' => 'Dosen Pengajar Dua',
               'nama_kegiatan' => 'Workshop Data Science for Industry 4.0',
               'nama_agenda' => 'Hands-on Data Analysis',
               'tanggal_agenda' => '2024-03-02',
               'created_at' => Carbon::now(),
               'updated_at' => Carbon::now()
           ],
           [
               'poin_id' => 8,
               'user_id' => 4,
               'agenda_id' => 8,
               'kegiatan_id' => 2,
               'jumlah_poin' => '5',
               'nidn' => '2233445566',
               'nama_lengkap' => 'Dosen Pengajar Dua',
               'nama_kegiatan' => 'Workshop Data Science for Industry 4.0',
               'nama_agenda' => 'Final Project & Presentation',
               'tanggal_agenda' => '2024-03-03',
               'created_at' => Carbon::now(),
               'updated_at' => Carbon::now()
           ]
       ];

       // Insert ke database
       DB::table('t_poin')->insert($data);
   }
}