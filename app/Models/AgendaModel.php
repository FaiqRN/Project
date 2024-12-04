<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'user_id',
        'kegiatan_luar_institusi_id',
        'kegiatan_institusi_id',
        'kegiatan_jurusan_id',
        'kegiatan_program_studi_id'
    ];

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
}