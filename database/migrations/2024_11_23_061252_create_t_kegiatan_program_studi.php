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
        Schema::create('t_kegiatan_program_studi', function (Blueprint $table) {
            $table->id('kegiatan_program_studi_id');
            $table->unsignedBigInteger('surat_id');
            $table->unsignedBigInteger('user_id');

            $table->string('nama_kegiatan_program_studi', 200);
            $table->text('deskripsi_kegiatan');
            $table->text('lokasi_kegiatan');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->enum('status_kegiatan', ['berlangsung', 'berakhir'])->default('berlangsung');
            $table->string('penyelenggara', 150)->nullable();
            $table->text('surat_penugasan')->nullable();
            $table->timestamps();


            $table->foreign('user_id')
                  ->references('user_id')
                  ->on('m_user')
                  ->onDelete('restrict');

            $table->foreign('surat_id')
                  ->references('surat_id')
                  ->on('m_surat')
                  ->onDelete('restrict');
        });;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_kegiatan_program_studi');
    }
};
