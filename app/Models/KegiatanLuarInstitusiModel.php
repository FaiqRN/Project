<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;


class KegiatanLuarInstitusiModel extends Model
{
    protected $table = 't_kegiatan_luar_institusi';
    protected $primaryKey = 'kegiatan_luar_institusi_id';
   
    protected $fillable = [
        'surat_id',
        'user_id',
        'nama_kegiatan_luar_institusi',
        'deskripsi_kegiatan',
        'lokasi_kegiatan',
        'tanggal_mulai',
        'tanggal_selesai',
        'status_kegiatan',
        'status_persetujuan',
        'keterangan',
        'penyelenggara',
        'created_by'
    ];


    protected $dates = [
        'tanggal_mulai',
        'tanggal_selesai'
    ];


    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'user_id');
    }


    public function surat()
    {
        return $this->belongsTo(SuratModel::class, 'surat_id', 'surat_id')->withDefault(null);
    }


    public function jabatan()
    {
        return $this->hasMany(JabatanModel::class, 'kegiatan_luar_institusi_id');
    }

    public function agendas()
    {
        return $this->hasMany(AgendaModel::class, 'kegiatan_luar_institusi_id');
    }

    public function finalDocument()
    {
        return $this->hasOne(FinalDocumentModel::class, 'kegiatan_luar_institusi_id');
    }

    public function checkStatus()
    {
        // Cek semua agenda
        $allAgendas = $this->agendas;
        $allCompleted = true;
        
        if($allAgendas->isEmpty()) {
            $allCompleted = false;
        }

        foreach($allAgendas as $agenda) {
            if($agenda->status_agenda != 'selesai') {
                $allCompleted = false;
                break;
            }
        }

        // Cek dokumen final
        $hasFinalDocument = $this->finalDocument()->exists();

        // Update status kegiatan
        if($allCompleted && $hasFinalDocument) {
            $this->status_kegiatan = 'selesai';
        } else {
            $this->status_kegiatan = 'berlangsung';
        }
        
        $this->save();
    }
}