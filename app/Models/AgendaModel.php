<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgendaModel extends Model
{
    protected $table = 't_agenda';
    protected $primaryKey = 'agenda_id';
    
    protected $fillable = [
        'kegiatan_id',
        'user_id',
        'poin_id',
        'dokumentasi_id',
        'nama_kegiatan',
        'nama_agenda',
        'file_surat_agenda',
        'nidn',
        'nama_lengkap',
        'nama_kelompok',
        'program_studi',
        'deskripsi',
        'nama_dokumentasi',
        'file_dokumentasi',
        'tanggal_agenda'
    ];

    public function kegiatan()
    {
        return $this->belongsTo(KegiatanModel::class, 'kegiatan_id', 'kegiatan_id');
    }

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'user_id');
    }

    public function dokumentasi()
    {
        return $this->belongsTo(DokumentasiModel::class, 'dokumentasi_id', 'dokumentasi_id');
    }

    public function poins()
    {
        return $this->hasMany(PoinModel::class, 'agenda_id', 'agenda_id');
    }
}