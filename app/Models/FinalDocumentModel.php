<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinalDocumentModel extends Model
{
    protected $table = 'm_final';
    protected $primaryKey = 'final_id';
    protected $fillable = ['file_akhir', 'kegiatan_jurusan_id', 'kegiatan_program_studi_id'];

    public function kegiatanJurusan()
    {
        return $this->belongsTo(KegiatanJurusanModel::class, 'kegiatan_jurusan_id');
    }

    public function kegiatanProdi()
    {
        return $this->belongsTo(KegiatanProgramStudiModel::class, 'kegiatan_program_studi_id');
    }
}
