<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('t_agenda', function (Blueprint $table) {
            $table->unsignedBigInteger('dokumentasi_id')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('t_agenda', function (Blueprint $table) {
            $table->unsignedBigInteger('dokumentasi_id')->nullable(false)->change();
        });
    }
};