<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->unsignedDecimal('price')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('coupons', function (Blueprint $table) {
            //
        });
    }
};
