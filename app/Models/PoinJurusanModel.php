<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PoinJurusanModel extends Model
{
    protected $table = 't_poin_jurusan';
    protected $primaryKey = 'poin_jurusan_id';
    
    protected $fillable = [
        'jabatan_id',
        'kegiatan_jurusan_id',
        'poin_ketua_pelaksana',
        'poin_sekertaris',
        'poin_bendahara',
        'poin_anggota',
        'total_poin',
        'poin_tambahan',
        'keterangan_tambahan',
        'status_poin_tambahan',
        'approved_by',
        'approved_at'
    ];

    protected $dates = [
        'approved_at',
        'created_at',
        'updated_at'
    ];

    // Relasi ke jabatan
    public function jabatan()
    {
        return $this->belongsTo(JabatanModel::class, 'jabatan_id');
    }

    // Relasi ke kegiatan jurusan
    public function kegiatanJurusan()
    {
        return $this->belongsTo(KegiatanJurusanModel::class, 'kegiatan_jurusan_id');
    }

    // Relasi ke user yang menyetujui
    public function approver()
    {
        return $this->belongsTo(UserModel::class, 'approved_by', 'user_id');
    }

    // Method untuk menghitung total poin
    public function hitungTotalPoin()
    {
        $poinDasar = 0;
        
        switch ($this->jabatan->jabatan) {
            case 'ketua_pelaksana':
                $poinDasar = $this->poin_ketua_pelaksana;
                break;
            case 'sekertaris':
                $poinDasar = $this->poin_sekertaris;
                break;
            case 'bendahara':
                $poinDasar = $this->poin_bendahara;
                break;
            case 'anggota':
                $poinDasar = $this->poin_anggota;
                break;
        }

        return $poinDasar + ($this->poin_tambahan ?? 0);
    }
}