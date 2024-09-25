<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('user_devices', function (Blueprint $table) {
            $table->string('browser')->nullable();
            // ------------------------------------ \\
            $table->text('session_token');
            // ------------------------------------ \\
            $table->text('device_token');
            // ------------------------------------ \\
            $table->string('device')->nullable();
            // ------------------------------------ \\
            $table->string('os')->nullable();
            $table->string('ip')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            // ------------------------------------ \\
            $table->point('location')->nullable();
            // ------------------------------------ \\
            $table->unsignedBigInteger('session_start_at')->nullable();
            $table->unsignedBigInteger('session_end_at')->nullable();
            // ------------------------------------ \\
            $table->enum("end_session_type", ['default', 'by_admin', 'by_user'])->nullable();
            // ------------------------------------ \\
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::table('user_devices', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn([
                'browser',
                'session_token',
                'device',
                'os',
                'ip',
                'city',
                'country',
                'location',
                'session_start_at',
                'session_end_at',
                'end_session_type',
            ]);
        });
    }
};
