<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Laravel\Sanctum\HasApiTokens;
class UserModel extends Model
{

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
    public function getLevelNamaAttribute()
    {
        return $this->level ? $this->level->level_nama : null;
    }

    public function level()
    {
        return $this->belongsTo(LevelModel::class, 'level_id', 'level_id');
    }

    // Tambahkan method ini di UserModel
    public function agendas()
    {
        return $this->belongsToMany(AgendaModel::class, 't_agenda_user', 'user_id', 'agenda_id')
                    ->withTimestamps();
    }

    public function dokumentasi()
{
    return $this->hasMany(DokumentasiModel::class, 'user_id', 'user_id');
}
}