<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up()
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->unsignedBigInteger('teacher_id')->nullable()->onDelete('set null')->change();
        });
    }


    public function down()
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->foreignId('teacher_id')
                ->constrained('teachers')
                ->onDelete('restrict')
                ->change();
        });
    }
};
