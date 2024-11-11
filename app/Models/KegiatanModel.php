<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KegiatanModel extends Model
{
    protected $table = 't_kegiatan';
    protected $primaryKey = 'kegiatan_id';
    
    protected $fillable = [
        'surat_id',
        'user_id',
        'nama_kegiatan',
        'deskripsi_kegiatan',
        'tempat_kegiatan',
        'tanggal_mulai',
        'tanggal_selesai',
        'bobot',
        'nama_kelompok',
        'nidn',
        'nama_lengkap',
        'program_studi',
        'status',
        'file_surat'
    ];

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'user_id');
    }

    public function surat()
    {
        return $this->belongsTo(SuratModel::class, 'surat_id', 'surat_id');
    }

    public function agendas()
    {
        return $this->hasMany(AgendaModel::class, 'kegiatan_id', 'kegiatan_id');
    }

    public function poins()
    {
        return $this->hasMany(PoinModel::class, 'kegiatan_id', 'kegiatan_id');
    }
}