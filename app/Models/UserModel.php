<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
class UserModel extends Model
{
    use SoftDeletes;
    use HasApiTokens;
    protected $table = 'm_user';
    protected $primaryKey = 'user_id';
    
    protected $fillable = [
        'level_id',
        'username',
        'foto',
        'password',
        'nidn',
        'nama_lengkap',
        'gelar_depan',
        'gelar_belakang',
        'jenis_kelamin',
        'jabatan_fungsional',
        'program_studi',
        'pendidikan_terakhir',
        'asal_perguruan_tinggi',
        'tempat_lahir',
        'tanggal_lahir',
        'agama',
        'status_nikah',
        'status_ikatan_kerja',
        'alamat',
        'email',
        'created_by',
        'updated_by',
        'last_activity',
        'deleted_by'
    ];
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'last_activity'
    ];
    public function level()
    {
        return $this->belongsTo(LevelModel::class, 'level_id', 'level_id');
    }

    public function kegiatans()
    {
        return $this->hasMany(KegiatanModel::class, 'user_id', 'user_id');
    }

    public function nonJTIs()
    {
        return $this->hasMany(NonJTIModel::class, 'user_id', 'user_id');
    }

    public function agendas()
    {
        return $this->hasMany(AgendaModel::class, 'user_id', 'user_id');
    }

    public function poins()
    {
        return $this->hasMany(PoinModel::class, 'user_id', 'user_id');
    }
}