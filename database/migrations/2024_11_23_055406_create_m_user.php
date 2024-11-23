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
        Schema::create('m_user', function (Blueprint $table) {
            $table->id('user_id');
            $table->unsignedBigInteger('level_id');            
            $table->string('username', 20)->unique();
            $table->text('foto')->nullable();
            $table->string('password');
            $table->string('nidn', 18)->unique();
            $table->string('nama_lengkap', 100);
            $table->string('gelar_depan', 50)->nullable();
            $table->string('gelar_belakang', 50)->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->string('jabatan_fungsional', 100);
            $table->string('program_studi', 100);
            $table->enum('pendidikan_terakhir', ['S1', 'S2', 'S3', 'Profesor'])->nullable();
            $table->string('asal_perguruan_tinggi', 100)->nullable();
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('agama', 50)->nullable();
            $table->enum('status_nikah', ['Menikah', 'Belum Menikah', 'Cerai'])->nullable();
            $table->string('status_ikatan_kerja', 100)->nullable();
            $table->text('alamat')->nullable();
            $table->string('email', 100)->nullable();
            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();
        
            $table->foreign('level_id')
                  ->references('level_id')
                  ->on('m_level')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');
                  
            $table->index(['nama_lengkap', 'nidn']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_user');
    }
};
