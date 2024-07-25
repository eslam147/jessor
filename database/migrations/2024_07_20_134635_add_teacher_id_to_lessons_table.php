<?php

use App\Enums\Lesson\LessonStatus;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->foreignId('teacher_id')->nullable()->constrained('teachers');
            $table->enum('status', LessonStatus::values())->default(LessonStatus::DRAFT->value);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropForeign(['teacher_id']);
            $table->dropColumn('teacher_id');
            $table->dropColumn('status');
        });
    }
};
