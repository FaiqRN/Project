<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluasiSawModel extends Model
{
    protected $table = 't_evaluasi_saw';
    protected $primaryKey = 'evaluasi_id';

    protected $fillable = [
        'periode_mulai',
        'periode_selesai'
    ];

    protected $dates = [
        'periode_mulai',
        'periode_selesai'
    ];

    // Relasi ke hasil SAW
    public function hasilSAW()
    {
        return $this->hasMany(HasilSawModel::class, 'evaluasi_id', 'evaluasi_id');
    }
}
