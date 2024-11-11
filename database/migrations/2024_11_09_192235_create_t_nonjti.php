<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration{

    public function up(): void{

        Schema::create('t_non_jti', function (Blueprint $table) {
            $table->id('non_jti_id');
            $table->unsignedBigInteger('user_id');
            $table->string('nama_lengkap', 100);
            $table->string('nama_kegiatan', 100);
            $table->text('deskripsi_kegiatan')->nullable();
            $table->enum('status_kegiatan', ['Direncanakan', 'Berlangsung', 'Selesai', 'Dibatalkan'])->default('Direncanakan');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->string('lokasi_kegiatan', 200)->nullable();
            $table->string('penyelenggara', 150)->nullable();
            $table->decimal('biaya_kegiatan', 15, 2)->nullable();
            $table->text('dokumen_pendukung')->nullable();
            $table->text('catatan')->nullable();
            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')
                  ->references('user_id')
                  ->on('m_user')
                  ->onDelete('cascade');
                  
            $table->index(['user_id', 'tanggal_mulai', 'tanggal_selesai']);
            $table->index('status_kegiatan');
            $table->index('created_at');
        });
    }

    public function down(): void{

        Schema::dropIfExists('t_non_jti');
    }
};