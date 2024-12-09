<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class AgendaModel extends Model
{
    protected $table = 't_agenda';
    protected $primaryKey = 'agenda_id';
    
    protected $fillable = [
        'nama_agenda',
        'tanggal_agenda',
        'file_surat_agenda',
        'deskripsi',
        'dokumentasi_id',
        'status_agenda',
        'user_id',
        'kegiatan_luar_institusi_id',
        'kegiatan_institusi_id',
        'kegiatan_jurusan_id',
        'kegiatan_program_studi_id'
    ];
    public $timestamps = true;

    protected $attributes = [
        'dokumentasi_id' => null
    ];

    // Relasi dengan User
    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'user_id');
    }

    // Relasi dengan Dokumentasi
    public function dokumentasi()
    {
        return $this->belongsTo(DokumentasiModel::class, 'dokumentasi_id', 'dokumentasi_id');
    }

    // Relasi dengan Kegiatan Luar Institusi
    public function kegiatanLuarInstitusi()
    {
        return $this->belongsTo(KegiatanLuarInstitusiModel::class, 'kegiatan_luar_institusi_id', 'kegiatan_luar_institusi_id');
    }

    // Relasi dengan Kegiatan Institusi
    public function kegiatanInstitusi()
    {
        return $this->belongsTo(KegiatanInstitusiModel::class, 'kegiatan_institusi_id', 'kegiatan_institusi_id');
    }

    // Relasi dengan Kegiatan Jurusan
    public function kegiatanJurusan()
    {
        return $this->belongsTo(KegiatanJurusanModel::class, 'kegiatan_jurusan_id', 'kegiatan_jurusan_id');
    }

    // Relasi dengan Kegiatan Program Studi
    public function kegiatanProgramStudi()
    {
        return $this->belongsTo(KegiatanProgramStudiModel::class, 'kegiatan_program_studi_id', 'kegiatan_program_studi_id');
    }

    // Method untuk mendapatkan kegiatan yang terkait
    public function getKegiatan()
    {
        if ($this->kegiatan_luar_institusi_id) {
            return $this->kegiatanLuarInstitusi;
        } elseif ($this->kegiatan_institusi_id) {
            return $this->kegiatanInstitusi;
        } elseif ($this->kegiatan_jurusan_id) {
            return $this->kegiatanJurusan;
        } elseif ($this->kegiatan_program_studi_id) {
            return $this->kegiatanProgramStudi;
        }
        return null;
    }

    // Method untuk mendapatkan tipe kegiatan
    public function getTipeKegiatan()
    {
        if ($this->kegiatan_luar_institusi_id) {
            return 'luar_institusi';
        } elseif ($this->kegiatan_institusi_id) {
            return 'institusi';
        } elseif ($this->kegiatan_jurusan_id) {
            return 'jurusan';
        } elseif ($this->kegiatan_program_studi_id) {
            return 'program_studi';
        }
        return null;
    }

    public function users()
    {
        return $this->belongsToMany(UserModel::class, 't_agenda_user', 'agenda_id', 'user_id')
                    ->withTimestamps();
    }

    public function agenda()
    {
        return $this->belongsTo(AgendaModel::class, 'agenda_id', 'agenda_id');
    }

    protected $appends = ['progress', 'display_status'];

    public function getProgressAttribute()
    {
        $totalUsers = $this->users()->count();
        $uploadedUsers = DokumentasiModel::where('agenda_id', $this->agenda_id)
                                       ->distinct('user_id')
                                       ->count('user_id');
        $progressPercentage = $totalUsers > 0 ? 
                             round(($uploadedUsers / $totalUsers) * 100, 2) : 0;

        return [
            'total_users' => $totalUsers,
            'uploaded_users' => $uploadedUsers,
            'percentage' => $progressPercentage
        ];
    }

    public function getDisplayStatusAttribute()
    {
        $progress = $this->progress;
        if ($progress['uploaded_users'] === 0) return 'berlangsung';
        if ($progress['uploaded_users'] === $progress['total_users']) return 'selesai';
        return 'tahap penyelesaian';
    }

    public function updateStatus()
    {
        $totalUsers = $this->users()->count();
        $uploadedUsers = DokumentasiModel::where('agenda_id', $this->agenda_id)
                                       ->distinct('user_id')
                                       ->count('user_id');
 
        if ($uploadedUsers == 0) {
            $this->status_agenda = 'berlangsung';
        } elseif ($uploadedUsers == $totalUsers) {
            $this->status_agenda = 'selesai';
        } else {
            $this->status_agenda = 'tahap penyelesaian';
        }
 
        $this->save();
    }

    protected static function boot()
    {
        parent::boot();
 
        // Event ketika agenda dihapus
        static::deleting(function($agenda) {
            // Hapus relasi dengan users
            $agenda->users()->detach();
            
            // Hapus dokumentasi terkait
            DokumentasiModel::where('agenda_id', $agenda->agenda_id)->delete();
            
            // Hapus file surat agenda jika ada
            if ($agenda->file_surat_agenda && Storage::exists($agenda->file_surat_agenda)) {
                Storage::delete($agenda->file_surat_agenda);
            }
        });
    }
}