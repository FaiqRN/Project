<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PoinLuarInstitusiModel extends Model
{
    protected $table = 't_poin_luar_institusi';
    protected $primaryKey = 'poin_luar_institusi_id';
    
    protected $fillable = [
        'jabatan_id',
        'kegiatan_luar_institusi_id',
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

    public function jabatan()
    {
        return $this->belongsTo(JabatanModel::class, 'jabatan_id');
    }

    public function kegiatan()
    {
        return $this->belongsTo(KegiatanLuarInstitusiModel::class, 'kegiatan_luar_institusi_id');
    }

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

    // Perlu disesuaikan nama method untuk konsistensi
    public function kegiatanLuarInstitusi() // seharusnya ini, bukan kegiatan()
    {
        return $this->belongsTo(KegiatanLuarInstitusiModel::class, 'kegiatan_luar_institusi_id');
    }

    public function canAddPoints()
    {
    return $this->kegiatan->status_kegiatan === 'selesai' && 
        empty($this->status_poin_tambahan);
    }
}