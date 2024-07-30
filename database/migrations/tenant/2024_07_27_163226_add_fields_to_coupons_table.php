<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->foreignId('class_id')->after('type')->nullable()->constrained('classes')->nullOnDelete();
            $table->foreignId('subject_id')->after('type')->nullable()->constrained('subjects')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropForeign(['class_id']);
            $table->dropForeign(['subject_id']);
            $table->dropColumn(['class_id','subject_id']);
        });
    }
};
