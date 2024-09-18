<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('online_exams', function (Blueprint $table) {
            $table->unsignedBigInteger('exam_key')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('online_exams', function (Blueprint $table) {
            $table->unsignedBigInteger('exam_key')->change();
        });
    }
};
