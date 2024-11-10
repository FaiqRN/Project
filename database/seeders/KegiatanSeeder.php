<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KegiatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'kegiatan_id' => 1,
                'surat_id' => 1, // Mengacu ke surat Seminar AI
                'nama_kegiatan' => 'Seminar Nasional Artificial Intelligence',
                'deskripsi_kegiatan' => 'Seminar nasional yang membahas perkembangan terbaru dalam bidang Artificial Intelligence (AI) dan implementasinya di Indonesia. Acara ini menghadirkan pembicara-pembicara ahli dari berbagai institusi dan industri yang akan berbagi pengetahuan dan pengalaman mereka dalam mengembangkan dan menerapkan teknologi AI.',
                'tempat_kegiatan' => 'Hotel Grand Mercure Jakarta, Jl. Hayam Wuruk No. 123, Jakarta Pusat',
                'tanggal_mulai' => '2024-01-15',
                'tanggal_selesai' => '2024-01-16',
                'bobot' => 'sedang',
                'status' => 'selesai',
                'file_surat' => 'surat_tugas_seminar_ai.pdf',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'kegiatan_id' => 2,
                'surat_id' => 2, // Mengacu ke surat Workshop Data Science
                'nama_kegiatan' => 'Workshop Data Science for Industry 4.0',
                'deskripsi_kegiatan' => 'Workshop praktis yang fokus pada implementasi Data Science dalam konteks Industri 4.0. Para peserta akan mendapatkan pengalaman hands-on dalam menggunakan berbagai tools dan teknologi terkini untuk analisis data dan machine learning. Workshop ini mencakup studi kasus nyata dari industri.',
                'tempat_kegiatan' => 'Gedung Informatika ITS, Kampus ITS Sukolilo, Surabaya',
                'tanggal_mulai' => '2024-03-01',
                'tanggal_selesai' => '2024-03-03',
                'bobot' => 'berat',
                'status' => 'berlangsung',
                'file_surat' => 'surat_tugas_workshop_ds.pdf',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        ];

        DB::table('t_kegiatan')->insert($data);
    }
}