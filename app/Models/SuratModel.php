<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SuratModel extends Model
{

    
    protected $table = 'm_surat';
    protected $primaryKey = 'surat_id';
    
    protected $fillable = [
        'nomer_surat',
        'judul_surat',
        'file_surat',
        'tanggal_surat'
    ];
    protected $dates = [
        'tanggal_surat',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function kegiatanJurusan()
    {
        return $this->hasOne(KegiatanJurusanModel::class, 'surat_id');
    }

    // Relasi ke KegiatanProgramStudi
    public function kegiatanProgramStudi()
    {
        return $this->hasOne(KegiatanProgramStudiModel::class, 'surat_id');
    }
}