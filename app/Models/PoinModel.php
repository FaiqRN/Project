<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PoinModel extends Model
{
    protected $table = 't_poin';
    protected $primaryKey = 'poin_id';
    
    protected $fillable = [
        'user_id',
        'agenda_id',
        'kegiatan_id',
        'jumlah_poin',
        'nidn',
        'nama_lengkap',
        'nama_kegiatan',
        'nama_agenda',
        'tanggal_agenda'
    ];

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'user_id');
    }

    public function agenda()
    {
        return $this->belongsTo(AgendaModel::class, 'agenda_id', 'agenda_id');
    }

    public function kegiatan()
    {
        return $this->belongsTo(KegiatanModel::class, 'kegiatan_id', 'kegiatan_id');
    }
}