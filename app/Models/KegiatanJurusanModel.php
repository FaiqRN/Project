<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KegiatanJurusanModel extends Model
{
    protected $table = 't_kegiatan_jurusan';
    protected $primaryKey = 'kegiatan_jurusan_id';
    
    protected $fillable = [
        'surat_id',
        'user_id',
        'nama_kegiatan_jurusan',
        'deskripsi_kegiatan',
        'lokasi_kegiatan',
        'tanggal_mulai',
        'tanggal_selesai',
        'status_kegiatan',
        'penyelenggara',
        'surat_penugasan'
    ];

    protected $dates = [
        'tanggal_mulai',
        'tanggal_selesai'
    ];

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'user_id');
    }

    public function surat()
    {
        return $this->belongsTo(SuratModel::class, 'surat_id', 'surat_id');
    }

    public function jabatan()
    {
        return $this->hasMany(JabatanModel::class, 'kegiatan_jurusan_id');
    }
}