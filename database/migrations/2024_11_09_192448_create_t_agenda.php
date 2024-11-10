<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('t_agenda', function (Blueprint $table) {
            $table->id('agenda_id');
            $table->unsignedBigInteger('kegiatan_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('poin_id');
            $table->unsignedBigInteger('dokumentasi_id');
            $table->string('nama_kegiatan',200);
            $table->string('nama_agenda',200);
            $table->string('nidn', 18)->unique();
            $table->string('nama_lengkap', 100);
            $table->string('nama_kelompok', 100);
            $table->string('program_studi', 100);
            $table->text('deskripsi');
            $table->string('nama_dokumentasi');
            $table->text('file_dokumentasi');
            $table->date('tanggal_agenda');
            $table->timestamps();

            $table->foreign('kegiatan_id')
                    ->references('kegiatan_id')
                    ->on('t_kegiatan')
                    ->onDelete('cascade');
            
            $table->foreign('user_id')
                    ->references('user_id')
                    ->on('m_user')
                    ->onDelete('restrict');

            $table->foreign('dokumentasi_id')
                    ->references('dokumentasi_id')
                    ->on('m_dokumentasi')
                    ->onDelete('restrict');

            $table->index(['tanggal_agenda']);
            $table->index(['user_id', 'kegiatan_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_agenda');
    }
};
