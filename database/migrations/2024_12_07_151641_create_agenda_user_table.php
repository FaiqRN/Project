<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('t_agenda_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agenda_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->foreign('agenda_id')
                  ->references('agenda_id')
                  ->on('t_agenda')
                  ->onDelete('cascade');
            $table->foreign('user_id')
                  ->references('user_id')
                  ->on('m_user')
                  ->onDelete('cascade');
                  
            // Mencegah duplikasi data
            $table->unique(['agenda_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('t_agenda_user');
    }
};