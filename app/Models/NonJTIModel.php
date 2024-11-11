<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NonJTIModel extends Model
{
    use SoftDeletes;
    
    protected $table = 't_non_jti';
    protected $primaryKey = 'non_jti_id';
    
    protected $fillable = [
        'user_id',
        'nama_lengkap',
        'nama_kegiatan',
        'deskripsi_kegiatan',
        'status_kegiatan',
        'tanggal_mulai',
        'tanggal_selesai',
        'lokasi_kegiatan',
        'penyelenggara',
        'biaya_kegiatan',
        'dokumen_pendukung',
        'catatan',
        'created_by',
        'updated_by'
    ];

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'user_id');
    }
}