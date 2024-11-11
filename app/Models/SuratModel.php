<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SuratModel extends Model
{
    use SoftDeletes;
    
    protected $table = 'm_surat';
    protected $primaryKey = 'surat_id';
    
    protected $fillable = [
        'nomer_surat',
        'judul_surat',
        'file_surat',
        'tanggal_surat'
    ];

    public function kegiatans()
    {
        return $this->hasMany(KegiatanModel::class, 'surat_id', 'surat_id');
    }
}