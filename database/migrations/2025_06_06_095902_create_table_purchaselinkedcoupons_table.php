<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablePurchaselinkedcouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('table_purchaselinkedcoupons', function (Blueprint $table) {
            $table->id();
            $table->integer('purchased_camp_id');
            $table->string('coupon_code')->index();
            $table->dateTime('coupon_expires')->nullable();
            $table->integer('is_claimed')->default(0);
            $table->integer('status')->default(1);
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
        Schema::dropIfExists('table_purchaselinkedcoupons');
    }
}
