<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupon_usages', function (Blueprint $table) {
            $table->id();
            # --------------------------------------------------------- #
            $table->foreignId('coupon_id')->constrained('coupons')->onDelete('cascade');
            # --------------------------------------------------------- #
            $table->morphs('used_by_user');
            # --------------------------------------------------------- #
            $table->morphs('applied_to');
            # --------------------------------------------------------- #
            $table->unsignedDecimal('amount', 10, 3);
            # --------------------------------------------------------- #
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
        Schema::dropIfExists('coupon_usages');
    }
};
