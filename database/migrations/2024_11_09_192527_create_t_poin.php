<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration{

    public function up(): void{

        Schema::create('t_poin', function (Blueprint $table) {
            $table->id('poin_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('agenda_id');
            $table->unsignedBigInteger('kegiatan_id');
            $table->string('jumlah_poin');
            $table->string('nidn');
            $table->string('nama_lengkap', 100);
            $table->string('nama_kegiatan',200);
            $table->string('nama_agenda',200);
            $table->date('tanggal_agenda');
            $table->timestamps();

            $table->foreign('user_id')
                    ->references('user_id')
                    ->on('m_user')
                    ->onDelete('restrict');

            $table->foreign('agenda_id')
                    ->references('agenda_id')
                    ->on('t_agenda')
                    ->onDelete('cascade');

            $table->foreign('kegiatan_id')
                    ->references('kegiatan_id')
                    ->on('t_kegiatan')
                    ->onDelete('cascade');

            $table->index(['user_id', 'agenda_id']);
            $table->index('tanggal_agenda');
        });
    }

    public function down(): void{
        
        Schema::dropIfExists('t_poin');
    }
};
