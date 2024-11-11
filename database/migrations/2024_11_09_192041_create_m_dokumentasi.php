<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration{

    public function up(): void{

        Schema::create('m_dokumentasi', function (Blueprint $table) {
            $table->id('dokumentasi_id');
            $table->string('nama_dokumentasi');
            $table->text('file_dokumentasi');
            $table->date('tanggal');
            $table->timestamps();
        });
    }

    public function down(): void{
        
        Schema::dropIfExists('m_dokumentasi');
    }
};