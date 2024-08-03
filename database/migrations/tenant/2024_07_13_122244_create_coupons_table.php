<?php

use App\Enums\Course\CouponTypeEnum;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            # -------------------------------------------------- #
            $table->string('code')->unique();
            # -------------------------------------------------- #
            $table->timestamp('expiry_date')->nullable();
            # -------------------------------------------------- #
            $table->unsignedDecimal('maximum_discount')->default(0);
            $table->unsignedDecimal('price')->default(0);
            # -------------------------------------------------- #
            $table->boolean('is_disabled')->default(false);
            # -------------------------------------------------- #
            $table->foreignId('teacher_id')->constrained('teachers');
            $table->unsignedSmallInteger('maximum_usage')->default(1);
            # -------------------------------------------------- #
            $table->enum('type', CouponTypeEnum::values())->default(CouponTypeEnum::PURCHASE->value);
            # -------------------------------------------------- #
            $table->nullableMorphs('only_applied_to');
            # -------------------------------------------------- #
            $table->softDeletes();
            $table->timestamps();
            # -------------------------------------------------- #
        });
    }


    public function down()
    {
        Schema::dropIfExists('coupons');
    }
};
