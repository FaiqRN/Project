<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DokumentasiModel extends Model
{
    protected $table = 'm_dokumentasi';
    protected $primaryKey = 'dokumentasi_id';
    
    protected $fillable = [
        'nama_dokumentasi',
        'deskripsi_dokumentasi',
        'file_dokumentasi',
        'tanggal',
        'user_id',  
        'agenda_id'
    ];

    // Relasi dengan Agenda
    public function agendas()
    {
        return $this->hasMany(AgendaModel::class, 'dokumentasi_id', 'dokumentasi_id');
    }

    // Method untuk mendapatkan URL file
    public function getFileUrl()
    {
        if ($this->file_dokumentasi) {
            return asset('storage/' . str_replace('public/', '', $this->file_dokumentasi));
        }
        return null;
    }

    // Method untuk mengecek ekstensi file
    public function getFileExtension()
    {
        if ($this->file_dokumentasi) {
            return pathinfo($this->file_dokumentasi, PATHINFO_EXTENSION);
        }
        return null;
    }

    // Method untuk mengecek apakah file masih ada di storage
    public function fileExists()
    {
        if ($this->file_dokumentasi) {
            return Storage::exists($this->file_dokumentasi);
        }
        return false;
    }

    // Boot method untuk menghapus file saat model dihapus
    protected static function boot()
    {
        parent::boot();

        static::deleting(function($dokumentasi) {
            if ($dokumentasi->file_dokumentasi && Storage::exists($dokumentasi->file_dokumentasi)) {
                Storage::delete($dokumentasi->file_dokumentasi);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'user_id');
    }

    public function agenda()
    {
        return $this->belongsTo(AgendaModel::class, 'agenda_id', 'agenda_id');
    }
}