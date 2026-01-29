<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablePurchasecoinsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('table_purchasecoins', function (Blueprint $table) {
            $table->id();
            $table->integer('camp_id');
            $table->integer('user_id');
            $table->unsignedBigInteger('base_plan_id')->nullable();
            $table->string('base_plan_name')->nullable();
            $table->string('camp_title')->index();
            $table->text('camp_description')->nullable();
            $table->float('camp_ticket_price', 8, 2)->default(0.00);
            $table->integer('camp_coins_per_campaign')->default(0);
            $table->integer('camp_coupons_per_campaign')->default(0);
            $table->float('camp_max_coins_per_transaction', 10, 2)->default(00.00);
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('is_cart')->default(0);
            $table->string('razor_order_id')->nullable();
            $table->string('payment_id')->nullable();
            $table->string('razorpay_signature')->nullable();
            $table->string('razor_key')->nullable();
            $table->string('payment_status')->nullable();
            $table->integer('quantity')->default(1);
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
        Schema::dropIfExists('table_purchasecoins');
    }
}
