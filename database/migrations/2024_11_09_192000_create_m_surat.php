<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration{


    public function up(): void{

        Schema::create('m_surat', function (Blueprint $table) {
            $table->id('surat_id');
            $table->string('nomer_surat', 100)->unique();
            $table->string('judul_surat', 200);
            $table->text('file_surat');
            $table->date('tanggal_surat');
            
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void{
        
        Schema::dropIfExists('m_surat');
    }
};
