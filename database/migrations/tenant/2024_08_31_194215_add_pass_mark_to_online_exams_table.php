<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('online_exams', function (Blueprint $table) {
            $table->unsignedSmallInteger('pass_mark')->after('exam_key')->default(0);
        });
    }

    public function down()
    {
        Schema::table('online_exams', function (Blueprint $table) {
            $table->dropColumn('pass_mark');
        });
    }
};
