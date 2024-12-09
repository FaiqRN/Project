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
        Schema::create('m_dokumentasi', function (Blueprint $table) {
            $table->id('dokumentasi_id');
            $table->string('nama_dokumentasi');
            $table->text('deskripsi_dokumentasi');
            $table->text('file_dokumentasi');
            $table->date('tanggal');
            $table->unsignedBigInteger('user_id')->nullable(); // Tambahan
            $table->unsignedBigInteger('agenda_id')->nullable(); 
            $table->timestamps();



            $table->foreign('user_id')
            ->references('user_id')
            ->on('m_user')
            ->onDelete('set null');
            
            $table->foreign('agenda_id')
            ->references('agenda_id')
            ->on('t_agenda')
            ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_dokumentasi');
    }
};
