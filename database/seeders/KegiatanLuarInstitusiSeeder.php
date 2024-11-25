<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KegiatanLuarInstitusiSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'kegiatan_luar_institusi_id' => 1,
                'user_id' => 3, 
                'surat_id' => 1,
                'nama_kegiatan_luar_institusi' => 'Seminar Nasional Artificial Intelligence',
                'deskripsi_kegiatan' => 'Seminar membahas perkembangan AI di Indonesia',
                'lokasi_kegiatan' => 'Hotel Grand Mercure Jakarta',
                'tanggal_mulai' => '2024-01-20',
                'tanggal_selesai' => '2024-01-21',
                'status_kegiatan' => 'berakhir',
                'penyelenggara' => 'Universitas Indonesia',
                'surat_penugasan' => 'surat_tugas_ai.pdf',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kegiatan_luar_institusi_id' => 2,
                'user_id' => 4, 
                'surat_id' => 2,
                'nama_kegiatan_luar_institusi' => 'Workshop Data Science and Analytics',
                'deskripsi_kegiatan' => 'Workshop pengolahan data dan analisis menggunakan Python',
                'lokasi_kegiatan' => 'Institut Teknologi Sepuluh November',
                'tanggal_mulai' => '2024-02-25',
                'tanggal_selesai' => '2024-02-26',
                'status_kegiatan' => 'berlangsung',
                'penyelenggara' => 'ITS Surabaya',
                'surat_penugasan' => 'surat_tugas_ds.pdf',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kegiatan_luar_institusi_id' => 3,
                'user_id' => 5, 
                'surat_id' => 3,
                'nama_kegiatan_luar_institusi' => 'International Conference on Computer Science',
                'deskripsi_kegiatan' => 'Konferensi internasional bidang ilmu komputer',
                'lokasi_kegiatan' => 'Bali International Convention Center',
                'tanggal_mulai' => '2024-03-25',
                'tanggal_selesai' => '2024-03-27',
                'status_kegiatan' => 'berlangsung',
                'penyelenggara' => 'IEEE Indonesia Section',
                'surat_penugasan' => 'surat_tugas_conf.pdf',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        DB::table('t_kegiatan_luar_institusi')->insert($data);
    }
}