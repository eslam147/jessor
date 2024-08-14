<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->text('type');
            $table->text('message');
        });

        DB::table('settings')->insert([
            [
                'type' => 'school_name',
                'message' => 'Jessor',
            ],
            [
                'type' => 'school_email',
                'message' => 'info@jessor.com',
            ],
            [
                'type' => 'school_phone',
                'message' => '`1234567890',
            ],
            [
                'type' => 'school_address',
                'message' => 'Egypt,cairo',
            ],
            [
                'type' => 'time_zone',
                'message' => 'Africa/Cairo',
            ],
            [
                'type' => 'date_formate',
                'message' => 'd-m-Y',
            ],
            [
                'type' => 'time_formate',
                'message' => 'h:i A',
            ],
            [
                'type' => 'theme_color',
                'message' => '#4C5EA6',
            ],
            [
                'type' => 'update_warning_modal',
                'message' => 1
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
};
