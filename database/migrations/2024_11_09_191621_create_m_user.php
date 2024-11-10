<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
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
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('jabatan_fungsional', 100);
            $table->string('program_studi', 100);
            $table->enum('pendidikan_terakhir', ['S1', 'S2', 'S3', 'Profesor']);
            $table->string('asal_perguruan_tinggi', 100);
            $table->string('tempat_lahir', 100);
            $table->date('tanggal_lahir');
            $table->string('agama', 50);
            $table->enum('status_nikah', ['Menikah', 'Belum Menikah', 'Cerai']);
            $table->string('status_ikatan_kerja', 100);
            $table->text('alamat');
            $table->string('email', 100)->unique();
            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Definisi foreign key yang merujuk ke tabel 'level'
            $table->foreign('level_id')
                  ->references('level_id')
                  ->on('m_level')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');
                  
            $table->index(['nama_lengkap', 'nidn']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_user');
    }
};
