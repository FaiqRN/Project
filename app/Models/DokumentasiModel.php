<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DokumentasiModel extends Model
{
    protected $table = 'm_dokumentasi';
    protected $primaryKey = 'dokumentasi_id';
    
    protected $fillable = [
        'nama_dokumentasi',
        'file_dokumentasi',
        'tanggal'
    ];

    public function agendas()
    {
        return $this->hasMany(AgendaModel::class, 'dokumentasi_id', 'dokumentasi_id');
    }
}