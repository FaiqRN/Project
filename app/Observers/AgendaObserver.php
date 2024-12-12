<?php

namespace App\Observers;

use App\Models\AgendaModel;

class AgendaObserver 
{
    public function updated(AgendaModel $agenda)
    {
        // Check if status_agenda field was changed
        if ($agenda->isDirty('status_agenda')) {
            
            // Update kegiatan jurusan status if exists
            if ($agenda->kegiatan_jurusan_id) {
                $kegiatan = $agenda->kegiatanJurusan;
                $allAgendas = $kegiatan->agendas;
                
                $allCompleted = true;
                if (!$allAgendas->isEmpty()) {
                    foreach ($allAgendas as $agendaItem) {
                        if ($agendaItem->status_agenda !== 'selesai') {
                            $allCompleted = false;
                            break;
                        }
                    }
                } else {
                    $allCompleted = false;
                }

                $kegiatan->status_kegiatan = $allCompleted ? 'selesai' : 'berlangsung';
                $kegiatan->save();
            }
            
            // Update kegiatan program studi status if exists
            if ($agenda->kegiatan_program_studi_id) {
                $kegiatan = $agenda->kegiatanProgramStudi;
                $allAgendas = $kegiatan->agendas;
                
                $allCompleted = true;
                if (!$allAgendas->isEmpty()) {
                    foreach ($allAgendas as $agendaItem) {
                        if ($agendaItem->status_agenda !== 'selesai') {
                            $allCompleted = false;
                            break;
                        }
                    }
                } else {
                    $allCompleted = false;
                }

                $kegiatan->status_kegiatan = $allCompleted ? 'selesai' : 'berlangsung';
                $kegiatan->save();
            }
        }
    }
}