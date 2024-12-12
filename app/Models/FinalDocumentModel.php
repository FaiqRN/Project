<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinalDocumentModel extends Model
{
    protected $table = 'm_final';
    protected $primaryKey = 'final_id';
    protected $fillable = ['file_akhir', 'nama_file', 'kegiatan_jurusan_id', 'kegiatan_program_studi_id'];

    public function kegiatanJurusan()
    {
        return $this->belongsTo(KegiatanJurusanModel::class, 'kegiatan_jurusan_id');
    }

    public function kegiatanProdi()
    {
        return $this->belongsTo(KegiatanProgramStudiModel::class, 'kegiatan_program_studi_id');
    }

    protected static function boot()
    {
        parent::boot();
        
        // Ketika dokumen final disimpan
        static::saved(function($finalDoc) {
            if($finalDoc->kegiatan_jurusan_id) {
                $finalDoc->kegiatanJurusan->checkStatus();
            } elseif($finalDoc->kegiatan_program_studi_id) {
                $finalDoc->kegiatanProdi->checkStatus();
            }
        });

        // Ketika dokumen final dihapus
        static::deleted(function($finalDoc) {
            if($finalDoc->kegiatan_jurusan_id) {
                $finalDoc->kegiatanJurusan->checkStatus();
            } elseif($finalDoc->kegiatan_program_studi_id) {
                $finalDoc->kegiatanProdi->checkStatus();
            }
        });
    }
}
