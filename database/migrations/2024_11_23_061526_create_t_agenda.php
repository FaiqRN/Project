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
            $table->string('nama_agenda', 200);
            $table->date('tanggal_agenda');
            $table->text('file_surat_agenda')->nullable();
            $table->text('deskripsi');
            $table->enum('status_agenda', ['berlangsung','tahap penyelesaian', 'selesai'])->default('berlangsung');
            $table->unsignedBigInteger('dokumentasi_id')->nullable();;
            $table->unsignedBigInteger('user_id')->nullable();;
            
            // References ke berbagai jenis kegiatan
            $table->unsignedBigInteger('kegiatan_luar_institusi_id')->nullable();
            $table->unsignedBigInteger('kegiatan_institusi_id')->nullable();
            $table->unsignedBigInteger('kegiatan_jurusan_id')->nullable();
            $table->unsignedBigInteger('kegiatan_program_studi_id')->nullable();
            
            $table->timestamps();

            // Foreign keys
            $table->foreign('dokumentasi_id')
                  ->references('dokumentasi_id')
                  ->on('m_dokumentasi')
                  ->onDelete('restrict');
            $table->foreign('user_id')
                  ->references('user_id')
                  ->on('m_user')
                  ->onDelete('restrict');
            $table->foreign('kegiatan_luar_institusi_id')
                  ->references('kegiatan_luar_institusi_id')
                  ->on('t_kegiatan_luar_institusi')
                  ->onDelete('restrict');
            $table->foreign('kegiatan_institusi_id')
                  ->references('kegiatan_institusi_id')
                  ->on('t_kegiatan_institusi')
                  ->onDelete('restrict');
            $table->foreign('kegiatan_jurusan_id')
                  ->references('kegiatan_jurusan_id')
                  ->on('t_kegiatan_jurusan')
                  ->onDelete('restrict');
            $table->foreign('kegiatan_program_studi_id')
                  ->references('kegiatan_program_studi_id')
                  ->on('t_kegiatan_program_studi')
                  ->onDelete('restrict');

            $table->index(['tanggal_agenda']);
            $table->index(['user_id']);
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
