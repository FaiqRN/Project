<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JabatanModel extends Model
{
    protected $table = 't_jabatan';
    protected $primaryKey = 'jabatan_id';
    
    protected $fillable = [
        'user_id',
        'level_id', 
        'jabatan',
        'kegiatan_luar_institusi_id',
        'kegiatan_institusi_id',
        'kegiatan_jurusan_id',
        'kegiatan_program_studi_id'
    ];

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'user_id');
    }

    public function level()
    {
        return $this->belongsTo(LevelModel::class, 'level_id', 'level_id');
    }

    public function kegiatanLuarInstitusi()
    {
        return $this->belongsTo(KegiatanLuarInstitusiModel::class, 'kegiatan_luar_institusi_id');
    }
    
    public function kegiatanInstitusi() 
    {
        return $this->belongsTo(KegiatanInstitusiModel::class, 'kegiatan_institusi_id');
    }
    
    public function kegiatanJurusan()
    {
        return $this->belongsTo(KegiatanJurusanModel::class, 'kegiatan_jurusan_id');
    }
    
    public function kegiatanProgramStudi()
    {
        return $this->belongsTo(KegiatanProgramStudiModel::class, 'kegiatan_program_studi_id');
    }
}