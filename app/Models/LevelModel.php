<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LevelModel extends Model
{
    protected $table = 'm_level';
    protected $primaryKey = 'level_id';
    
    protected $fillable = [
        'level_kode',
        'level_nama'
    ];

    public function users()
    {
        return $this->hasMany(UserModel::class, 'level_id', 'level_id');
    }
}