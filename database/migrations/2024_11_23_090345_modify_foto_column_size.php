<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyFotoColumnSize extends Migration
{
    public function up()
    {
        Schema::table('m_user', function (Blueprint $table) {
            $table->mediumText('foto')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('m_user', function (Blueprint $table) {
            $table->text('foto')->nullable()->change();
        });
    }
}