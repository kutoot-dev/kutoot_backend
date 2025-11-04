<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRazorpayPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('razorpay_payments', function (Blueprint $table) {
            $table->id();
            $table->integer('status')->default(0);
            $table->string('name')->nullable();
            $table->double('currency_rate')->default(1);
            $table->string('description')->nullable();
            $table->string('image')->nullable();
            $table->string('color')->nullable();
            $table->text('key')->nullable();
            $table->text('secret_key')->nullable();
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
        Schema::dropIfExists('razorpay_payments');
    }
}
