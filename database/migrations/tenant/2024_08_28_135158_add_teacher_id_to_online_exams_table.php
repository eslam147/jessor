<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('online_exams', function (Blueprint $table) {
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->cascadeOnDelete();
        });
    }

    public function down()
    {
        Schema::table('online_exams', function (Blueprint $table) {
            $table->dropForeign(['teacher_id']);
            $table->dropColumn('teacher_id');
        });
    }
};
