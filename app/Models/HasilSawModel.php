<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HasilSawModel extends Model
{
    protected $table = 't_hasil_saw';
    protected $primaryKey = 'hasil_saw_id';

    protected $fillable = [
        'evaluasi_id',
        'user_id',
        'poin_dasar',
        'poin_tambahan',
        'status_poin',
        'nilai_normalisasi_dasar',
        'nilai_normalisasi_tambahan',
        'nilai_normalisasi_status',
        'nilai_akhir_saw',
        'ranking'
    ];

    // Relasi ke evaluasi SAW
    public function evaluasi()
    {
        return $this->belongsTo(EvaluasiSawModel::class, 'evaluasi_id', 'evaluasi_id');
    }

    // Relasi ke user
    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'user_id');
    }
}
