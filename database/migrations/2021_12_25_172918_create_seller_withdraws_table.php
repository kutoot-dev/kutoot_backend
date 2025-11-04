<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSellerWithdrawsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seller_withdraws', function (Blueprint $table) {
            $table->id();
            $table->integer('seller_id');
            $table->string('method');
            $table->double('total_amount');
            $table->double('withdraw_amount');
            $table->double('withdraw_charge');
            $table->text('account_info');
            $table->integer('status')->default(0);
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
        Schema::dropIfExists('seller_withdraws');
    }
}
