<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TotalPoinDosenModel extends Model
{
    protected $table = 't_total_poin_dosen';
    protected $primaryKey = 'total_poin_id';
    
    protected $fillable = [
        'user_id',
        'total_poin_jurusan',
        'total_poin_prodi',
        'total_poin_institusi',
        'total_poin_luar_institusi',
        'total_keseluruhan',
        'last_updated'
    ];

    protected $casts = [
        'total_poin_jurusan' => 'decimal:2',
        'total_poin_prodi' => 'decimal:2',
        'total_poin_institusi' => 'decimal:2',
        'total_poin_luar_institusi' => 'decimal:2',
        'total_keseluruhan' => 'decimal:2',
        'last_updated' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'user_id');
    }
}