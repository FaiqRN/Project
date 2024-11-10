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
        Schema::create('t_kegiatan', function (Blueprint $table) {
            $table->id('kegiatan_id');
            $table->unsignedBigInteger('surat_id');
            $table->unsignedBigInteger('user_id');
            $table->string('nama_kegiatan',200);
            $table->text('deskripsi_kegiatan');
            $table->text('tempat_kegiatan');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->enum('bobot', ['ringan', 'sedang', 'berat']);
            $table->string('nama_kelompok',50);
            $table->string('nidn', 18)->unique();
            $table->string('nama_lengkap', 100);
            $table->string('program_studi', 100);
            $table->enum('status',['selesai','berlangsung'])->default('berlangsung');
            $table->text('file_surat');
            $table->timestamps();

            $table->foreign('user_id')
                    ->references('user_id')
                    ->on('m_user')
                    ->onDelete('cascade');

            $table->foreign('surat_id')
                    ->references('surat_id')
                    ->on('m_surat')
                    ->onDelete('cascade');
    
            $table->index(['tanggal_mulai', 'tanggal_selesai']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_kegiatan');
    }
};
