<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('meetings', function (Blueprint $table) {
            $table->uuid("id")->primary();
            // --------------------------------------------------- \\
            $table->morphs('scheduler');
            // --------------------------------------------------- \\
            $table->string('topic',512);
            $table->string('provider')->comment('service of video conference');
            // --------------------------------------------------- \\
            $table->dateTimeTz('start_time');
            // --------------------------------------------------- \\
            $table->string('meeting_id')->unique()->nullable();
            $table->string('timezone')->nullable();
            // --------------------------------------------------- \\
            $table->dateTimeTz('started_at')->nullable();
            $table->dateTimeTz('ended_at')->nullable();
            $table->dateTimeTz('cancelled_at')->nullable();
            // --------------------------------------------------- \\
            $table->text('join_url');
            $table->text('start_url')->nullable();
            // --------------------------------------------------- \\
            $table->text('password')->nullable();
            $table->unsignedInteger('duration')->comment("in minutes")->default(0);
            // --------------------------------------------------- \\
            $table->json('meta')->nullable();
            // --------------------------------------------------- \\
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('meetings');
    }
};
