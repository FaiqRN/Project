<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AgendaSeeder extends Seeder
{
   public function run(): void
   {
       // Agenda untuk Kegiatan 1 (Seminar AI)
       $data = [
           [
               'agenda_id' => 1,
               'kegiatan_id' => 1,
               'user_id' => 3,
               'poin_id' => 1, // Asumsi poin_id sudah ada
               'dokumentasi_id' => 1, // Asumsi dokumentasi_id sudah ada
               'nama_kegiatan' => 'Seminar Nasional Artificial Intelligence',
               'nama_agenda' => 'Registrasi dan Pembukaan',
               'file_surat_agenda' => 'agenda_1_registrasi.pdf',
               'nidn' => '1122334455',
               'nama_lengkap' => 'Dosen Pengajar Satu',
               'nama_kelompok' => 'Dosen Group',
               'program_studi' => 'Teknologi Informasi',
               'deskripsi' => 'Registrasi peserta dan pembukaan acara oleh ketua panitia',
               'nama_dokumentasi' => 'Dokumentasi Registrasi',
               'file_dokumentasi' => 'dok_registrasi.jpg',
               'tanggal_agenda' => '2024-01-15 08:00:00',
               'created_at' => Carbon::now(),
               'updated_at' => Carbon::now()
           ],
           [
               'agenda_id' => 2,
               'kegiatan_id' => 1,
               'user_id' => 3,
               'poin_id' => 1,
               'dokumentasi_id' => 1,
               'nama_kegiatan' => 'Seminar Nasional Artificial Intelligence',
               'nama_agenda' => 'Keynote Speaker Session',
               'file_surat_agenda' => 'agenda_1_keynote.pdf',
               'nidn' => '1122334455',
               'nama_lengkap' => 'Dosen Pengajar Satu',
               'nama_kelompok' => 'Dosen Group',
               'program_studi' => 'Teknologi Informasi',
               'deskripsi' => 'Sesi presentasi oleh pembicara utama tentang perkembangan AI global',
               'nama_dokumentasi' => 'Dokumentasi Keynote',
               'file_dokumentasi' => 'dok_keynote.jpg',
               'tanggal_agenda' => '2024-01-15 09:00:00',
               'created_at' => Carbon::now(),
               'updated_at' => Carbon::now()
           ],
           [
               'agenda_id' => 3,
               'kegiatan_id' => 1,
               'user_id' => 3,
               'poin_id' => 1,
               'dokumentasi_id' => 1,
               'nama_kegiatan' => 'Seminar Nasional Artificial Intelligence',
               'nama_agenda' => 'Panel Discussion: AI in Industry',
               'file_surat_agenda' => 'agenda_1_panel.pdf',
               'nidn' => '1122334455',
               'nama_lengkap' => 'Dosen Pengajar Satu',
               'nama_kelompok' => 'Dosen Group',
               'program_studi' => 'Teknologi Informasi',
               'deskripsi' => 'Diskusi panel membahas implementasi AI di industri Indonesia',
               'nama_dokumentasi' => 'Dokumentasi Panel',
               'file_dokumentasi' => 'dok_panel.jpg',
               'tanggal_agenda' => '2024-01-15 13:00:00',
               'created_at' => Carbon::now(),
               'updated_at' => Carbon::now()
           ],
           [
               'agenda_id' => 4,
               'kegiatan_id' => 1,
               'user_id' => 3,
               'poin_id' => 1,
               'dokumentasi_id' => 1,
               'nama_kegiatan' => 'Seminar Nasional Artificial Intelligence',
               'nama_agenda' => 'Workshop AI Implementation',
               'file_surat_agenda' => 'agenda_1_workshop.pdf',
               'nidn' => '1122334455',
               'nama_lengkap' => 'Dosen Pengajar Satu',
               'nama_kelompok' => 'Dosen Group',
               'program_studi' => 'Teknologi Informasi',
               'deskripsi' => 'Sesi praktik implementasi AI menggunakan tools terkini',
               'nama_dokumentasi' => 'Dokumentasi Workshop',
               'file_dokumentasi' => 'dok_workshop.jpg',
               'tanggal_agenda' => '2024-01-16 09:00:00',
               'created_at' => Carbon::now(),
               'updated_at' => Carbon::now()
           ],
           [
               'agenda_id' => 5,
               'kegiatan_id' => 1,
               'user_id' => 3,
               'poin_id' => 1,
               'dokumentasi_id' => 1,
               'nama_kegiatan' => 'Seminar Nasional Artificial Intelligence',
               'nama_agenda' => 'Closing dan Networking',
               'file_surat_agenda' => 'agenda_1_closing.pdf',
               'nidn' => '1122334455',
               'nama_lengkap' => 'Dosen Pengajar Satu',
               'nama_kelompok' => 'Dosen Group',
               'program_studi' => 'Teknologi Informasi',
               'deskripsi' => 'Penutupan acara dan sesi networking antar peserta',
               'nama_dokumentasi' => 'Dokumentasi Closing',
               'file_dokumentasi' => 'dok_closing.jpg',
               'tanggal_agenda' => '2024-01-16 15:00:00',
               'created_at' => Carbon::now(),
               'updated_at' => Carbon::now()
           ],
       

       // Agenda untuk Kegiatan 2 (Workshop Data Science)
       
           [
               'agenda_id' => 6,
               'kegiatan_id' => 2,
               'user_id' => 4,
               'poin_id' => 2,
               'dokumentasi_id' => 2,
               'nama_kegiatan' => 'Workshop Data Science for Industry 4.0',
               'nama_agenda' => 'Introduction to Data Science',
               'file_surat_agenda' => 'agenda_2_intro.pdf',
               'nidn' => '2233445566',
               'nama_lengkap' => 'Dosen Pengajar Dua',
               'nama_kelompok' => 'Dosen Group',
               'program_studi' => 'Teknologi Informasi',
               'deskripsi' => 'Pengenalan dasar Data Science dan tools yang akan digunakan',
               'nama_dokumentasi' => 'Dokumentasi Intro DS',
               'file_dokumentasi' => 'dok_intro_ds.jpg',
               'tanggal_agenda' => '2024-03-01 09:00:00',
               'created_at' => Carbon::now(),
               'updated_at' => Carbon::now()
           ],
           [
               'agenda_id' => 7,
               'kegiatan_id' => 2,
               'user_id' => 4,
               'poin_id' => 2,
               'dokumentasi_id' => 2,
               'nama_kegiatan' => 'Workshop Data Science for Industry 4.0',
               'nama_agenda' => 'Hands-on Data Analysis',
               'file_surat_agenda' => 'agenda_2_handson.pdf',
               'nidn' => '2233445566',
               'nama_lengkap' => 'Dosen Pengajar Dua',
               'nama_kelompok' => 'Dosen Group',
               'program_studi' => 'Teknologi Informasi',
               'deskripsi' => 'Praktik analisis data menggunakan Python dan libraries Data Science',
               'nama_dokumentasi' => 'Dokumentasi Hands-on',
               'file_dokumentasi' => 'dok_handson.jpg',
               'tanggal_agenda' => '2024-03-02 09:00:00',
               'created_at' => Carbon::now(),
               'updated_at' => Carbon::now()
           ],
           [
               'agenda_id' => 8,
               'kegiatan_id' => 2,
               'user_id' => 4,
               'poin_id' => 2,
               'dokumentasi_id' => 2,
               'nama_kegiatan' => 'Workshop Data Science for Industry 4.0',
               'nama_agenda' => 'Final Project & Presentation',
               'file_surat_agenda' => 'agenda_2_final.pdf',
               'nidn' => '2233445566',
               'nama_lengkap' => 'Dosen Pengajar Dua',
               'nama_kelompok' => 'Dosen Group',
               'program_studi' => 'Teknologi Informasi',
               'deskripsi' => 'Pengerjaan dan presentasi project akhir workshop',
               'nama_dokumentasi' => 'Dokumentasi Final',
               'file_dokumentasi' => 'dok_final.jpg',
               'tanggal_agenda' => '2024-03-03 09:00:00',
               'created_at' => Carbon::now(),
               'updated_at' => Carbon::now()
           ]
       ];


       // Insert ke database
       DB::table('t_agenda')->insert($data);
   }
}