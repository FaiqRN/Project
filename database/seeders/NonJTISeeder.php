<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NonJTISeeder extends Seeder{
   
   public function run(): void{
       $data = [
           [
               'non_jti_id' => 1,
               'user_id' => 3, 
               'nama_lengkap' => 'Dosen Pengajar Satu',
               'nama_kegiatan' => 'Seminar Nasional Artificial Intelligence',
               'deskripsi_kegiatan' => 'Seminar nasional membahas perkembangan terbaru AI dan implementasinya di Indonesia',
               'status_kegiatan' => 'Selesai',
               'tanggal_mulai' => '2024-01-15',
               'tanggal_selesai' => '2024-01-16',
               'lokasi_kegiatan' => 'Hotel Grand Mercure Jakarta',
               'penyelenggara' => 'Asosiasi Kecerdasan Artifisial Indonesia',
               'biaya_kegiatan' => 2500000.00,
               'dokumen_pendukung' => 'seminar_ai_2024.pdf',
               'catatan' => 'Presentasi dan networking dengan praktisi AI',
               'created_by' => 'System',
               'updated_by' => 'System',
               'created_at' => Carbon::now(),
               'updated_at' => Carbon::now()
           ],
           [
               'non_jti_id' => 2,
               'user_id' => 4, 
               'nama_lengkap' => 'Dosen Pengajar Dua',
               'nama_kegiatan' => 'Workshop Data Science for Industry 4.0',
               'deskripsi_kegiatan' => 'Workshop praktis mengenai implementasi data science dalam industri 4.0',
               'status_kegiatan' => 'Berlangsung',
               'tanggal_mulai' => '2024-03-01',
               'tanggal_selesai' => '2024-03-03',
               'lokasi_kegiatan' => 'Institut Teknologi Sepuluh Nopember, Surabaya',
               'penyelenggara' => 'ITS Data Science Center',
               'biaya_kegiatan' => 3500000.00,
               'dokumen_pendukung' => 'workshop_ds_its.pdf',
               'catatan' => 'Pelatihan hands-on dengan tools data science terkini',
               'created_by' => 'System',
               'updated_by' => 'System',
               'created_at' => Carbon::now(),
               'updated_at' => Carbon::now()
           ],
           [
               'non_jti_id' => 3,
               'user_id' => 5, 
               'nama_lengkap' => 'Dosen Pengajar Tiga',
               'nama_kegiatan' => 'International Conference on Computer Science',
               'deskripsi_kegiatan' => 'Konferensi internasional tentang perkembangan ilmu komputer',
               'status_kegiatan' => 'Direncanakan',
               'tanggal_mulai' => '2024-07-20',
               'tanggal_selesai' => '2024-07-23',
               'lokasi_kegiatan' => 'Singapore International Convention Center',
               'penyelenggara' => 'IEEE Computer Society',
               'biaya_kegiatan' => 7500000.00,
               'dokumen_pendukung' => 'iccs_2024.pdf',
               'catatan' => 'Presentasi paper penelitian dan networking internasional',
               'created_by' => 'System',
               'updated_by' => 'System',
               'created_at' => Carbon::now(),
               'updated_at' => Carbon::now()
           ]
       ];

       DB::table('t_non_jti')->insert($data);
   }
}