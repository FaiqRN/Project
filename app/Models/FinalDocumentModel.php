<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinalDocumentModel extends Model
{
    protected $table = 'm_final';
    protected $primaryKey = 'final_id';
    protected $fillable = ['file_akhir', 'nama_file', 'kegiatan_jurusan_id', 'kegiatan_program_studi_id', 'kegiatan_institusi_id', 'kegiatan_luar_institusi_id'];

    public function kegiatanJurusan()
    {
        return $this->belongsTo(KegiatanJurusanModel::class, 'kegiatan_jurusan_id');
    }

    public function kegiatanProdi()
    {
        return $this->belongsTo(KegiatanProgramStudiModel::class, 'kegiatan_program_studi_id');
    }

    public function kegiatanInstitusi()
    {
        return $this->belongsTo(KegiatanInstitusiModel::class, 'kegiatan_institusi_id');
    }

    public function kegiatanLuarInstitusi( )
    {
        return $this->belongsTo(KegiatanLuarInstitusiModel::class, 'kegiatan_luar_institusi_id');
    }

    protected static function boot()
    {
        parent::boot();
        static::saving(function ($model) {
            if (!empty($model->kegiatan_jurusan_id) && !empty($model->kegiatan_program_studi_id) && !empty($model->kegiatan_institusi_id) && !empty($model->kegiatan_luar_institusi_id)) {
                throw new \Exception('Dokumen hanya boleh terkait dengan satu kegiatan');
            }
        });
        // Ketika dokumen final disimpan
        static::saved(function($finalDoc) {
            if($finalDoc->kegiatan_jurusan_id) {
                $finalDoc->kegiatanJurusan->checkStatus();
            } elseif($finalDoc->kegiatan_program_studi_id) {
                $finalDoc->kegiatanProdi->checkStatus();
            }elseif($finalDoc->kegiatan_institusi_id) {
                $finalDoc->kegiatanInstitusi->checkStatus();
            }elseif($finalDoc->kegiatan_luar_institusi_id) {
                $finalDoc->kegiatanLuarInstitusi->checkStatus();
            }
        });

        // Ketika dokumen final dihapus

    }
}
