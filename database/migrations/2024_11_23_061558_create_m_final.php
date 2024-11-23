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
        Schema::create('m_final', function (Blueprint $table) {
            $table->id('final_id');
            $table->text('file_akhir');
            
            // References ke berbagai jenis kegiatan
            $table->unsignedBigInteger('kegiatan_luar_institusi_id')->nullable();
            $table->unsignedBigInteger('kegiatan_institusi_id')->nullable();
            $table->unsignedBigInteger('kegiatan_jurusan_id')->nullable();
            $table->unsignedBigInteger('kegiatan_program_studi_id')->nullable();
            
            $table->timestamps();

            // Foreign keys
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
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_final');
    }
};
