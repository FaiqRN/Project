<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_evaluasi_saw', function (Blueprint $table) {
            $table->id('evaluasi_id');
            $table->date('periode_mulai');
            $table->date('periode_selesai');
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('t_evaluasi_saw');
    }
};

