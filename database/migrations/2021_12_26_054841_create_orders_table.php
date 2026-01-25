<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('zoho_salesorder_id')->nullable();
            $table->string('order_id');
            $table->integer('user_id');
            $table->double('amount_usd')->default(0);
            $table->double('amount_usd_currency')->default(0);
            $table->double('amount_real_currency')->default(0);
            $table->double('currency_rate')->default(0);
            $table->string('currency_name')->nullable();
            $table->string('currency_icon')->nullable();
            $table->integer('product_qty');
            $table->double('total_amount')->default(0);
            $table->double('sub_total')->default(0);
            $table->double('tax')->default(0);
            $table->double('coupon_discount')->default(0);
            $table->string('payment_method')->nullable();
            $table->string('payment_status')->default('pending');
            $table->string('payment_approval_date')->nullable();
            $table->integer('refound_status')->default(0);
            $table->string('payment_refound_date')->nullable();
            $table->string('transection_id')->nullable();
            $table->string('shipping_method')->nullable();
            $table->double('shipping_cost')->default(0);
            $table->double('coupon_coast')->default(0);
            $table->double('order_vat')->default(0);
            $table->string('order_status')->default('0');
            $table->string('order_approval_date')->nullable();
            $table->string('order_delivered_date')->nullable();
            $table->string('order_completed_date')->nullable();
            $table->string('order_declined_date')->nullable();
            $table->integer('delivery_man_id')->default(0);
            $table->integer('order_request')->default(0);
            $table->date('order_req_date')->nullable();
            $table->date('order_req_accept_date')->nullable();
            $table->string('zoho_invoice_id')->nullable();
            $table->string('zoho_shipment_id')->nullable();
            $table->string('payment_type')->nullable();
            $table->boolean('cash_on_delivery')->default(false);
            $table->timestamp('order_date')->nullable();
            $table->integer('order_month')->nullable();
            $table->integer('order_year')->nullable();
            $table->string('cancel_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->integer('reedem_coins')->default(0);
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
        Schema::dropIfExists('orders');
    }
}
