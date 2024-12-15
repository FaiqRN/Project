<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('t_total_poin_dosen', function (Blueprint $table) {
            $table->id('total_poin_id');
            $table->unsignedBigInteger('user_id');
            $table->decimal('total_poin_jurusan', 10, 2)->default(0);
            $table->decimal('total_poin_prodi', 10, 2)->default(0);
            $table->decimal('total_poin_institusi', 10, 2)->default(0);
            $table->decimal('total_poin_luar_institusi', 10, 2)->default(0);
            $table->decimal('total_keseluruhan', 10, 2)->default(0);
            $table->timestamp('last_updated')->useCurrent();
            $table->timestamps();

            $table->foreign('user_id')
                  ->references('user_id')
                  ->on('m_user')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('t_total_poin_dosen');
    }
};