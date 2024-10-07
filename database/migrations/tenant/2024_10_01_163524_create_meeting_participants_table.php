<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('meeting_participants', function (Blueprint $table) {
            $table->id();
            // ------------------------------------------- \\
            $table->dateTimeTz('joined_at')->nullable();
            // ------------------------------------------- \\
            $table->morphs('participant');
            $table->nullableMorphs('purchaseable');
            // ------------------------------------------- \\
            $table->foreignId('meeting_id')->constrained('meetings')->cascadeOnDelete();
            // ------------------------------------------- \\
            $table->unique(['participant_id', 'participant_type', 'meeting_id'], 'meeting_participant_unique_key');
            // ----------------------------------------------- \\
            $table->softDeletes();
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('meeting_participants');
    }
};
