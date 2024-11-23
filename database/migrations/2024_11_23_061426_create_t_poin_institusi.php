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
        Schema::create('t_poin_institusi', function (Blueprint $table) {
            $table->id('poin_institusi_id');
            $table->unsignedBigInteger('jabatan_id');
            $table->unsignedBigInteger('kegiatan_institusi_id');
            $table->integer('poin_ketua_pelaksana')->default(4);
            $table->integer('poin_sekertaris')->default(3.5);
            $table->integer('poin_bendahara')->default(3.5);
            $table->integer('poin_anggota')->default(3);
            $table->integer('total_poin');
            $table->integer('poin_tambahan')->default(0);
            $table->text('keterangan_tambahan')->nullable();
            $table->enum('status_poin_tambahan', ['pending', 'disetujui', 'ditolak'])->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->foreign('jabatan_id')
                  ->references('jabatan_id')
                  ->on('t_jabatan')
                  ->onDelete('cascade');
            
            $table->foreign('kegiatan_institusi_id')
                  ->references('kegiatan_institusi_id')
                  ->on('t_kegiatan_institusi')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_poin_institusi');
    }
};
