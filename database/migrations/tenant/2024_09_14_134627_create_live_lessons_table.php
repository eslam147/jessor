<?php

use App\Enums\Lesson\LiveLessonStatus;
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
        Schema::create('live_lessons', function (Blueprint $table) {
            $table->id();
            // --------------------------------------------------- \\
            $table->foreignId('teacher_id')->constrained('teachers');
            $table->foreignId('class_section_id')->constrained('class_sections');
            $table->foreignId('subject_id')->constrained('subjects');
            // --------------------------------------------------- \\
            $table->string('name', 512);
            $table->string('description', 1024)->nullable();
            // --------------------------------------------------- \\
            $table->enum('status', LiveLessonStatus::values())->default(LiveLessonStatus::DEFAULT );
            // --------------------------------------------------- \\
            $table->dateTimeTz('session_start_at');
            $table->unsignedInteger('duration')->comment("in minutes")->default(0);
            // --------------------------------------------------- \\
            $table->text('notes')->nullable();
            // --------------------------------------------------- \\
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('live_lessons');
    }
};
