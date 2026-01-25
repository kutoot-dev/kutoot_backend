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
            $table->string('coupon_code');
            $table->dateTime('coupon_expires')->nullable();
            $table->integer('coins')->default(0);
            $table->integer('is_claimed')->default(0);
            $table->integer('status')->default(1);
            $table->string('series_label')->nullable();
            $table->string('main_campaign_id')->nullable();
            $table->timestamps();

            // Unique constraint on combination of main_campaign_id, series_label and coupon_code
            $table->unique(['main_campaign_id', 'series_label', 'coupon_code'], 'unique_campaign_series_coupon');
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
