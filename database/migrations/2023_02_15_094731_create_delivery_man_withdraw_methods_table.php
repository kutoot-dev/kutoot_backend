<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryManWithdrawMethodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_man_withdraw_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->double('min_amount')->default(0);
            $table->double('max_amount')->default(0);
            $table->double('withdraw_charge')->default(0);
            $table->text('description')->nullable();
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
        Schema::dropIfExists('delivery_man_withdraw_methods');
    }
}
