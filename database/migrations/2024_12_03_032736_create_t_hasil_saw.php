<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_hasil_saw', function (Blueprint $table) {
            $table->id('hasil_saw_id');
            $table->unsignedBigInteger('evaluasi_id');
            $table->unsignedBigInteger('user_id');
            $table->decimal('poin_dasar', 5, 2);
            $table->decimal('poin_tambahan', 5, 2)->nullable();
            $table->enum('status_poin', ['disetujui', 'pending', 'ditolak'])->default('disetujui');
            $table->decimal('nilai_normalisasi_dasar', 5, 4);
            $table->decimal('nilai_normalisasi_tambahan', 5, 4);
            $table->decimal('nilai_normalisasi_status', 5, 4);
            $table->decimal('nilai_akhir_saw', 5, 4);
            $table->integer('ranking');
            $table->timestamps();


            $table->foreign('evaluasi_id')
                  ->references('evaluasi_id')
                  ->on('t_evaluasi_saw')
                  ->onDelete('cascade');


            $table->foreign('user_id')
                  ->references('user_id')
                  ->on('m_user')
                  ->onDelete('restrict');


            // Index untuk mempercepat query
            $table->index(['evaluasi_id', 'user_id']);
            $table->index('ranking');
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('t_hasil_saw');
    }
};



