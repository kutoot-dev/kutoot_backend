<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstamojoPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('instamojo_payments', function (Blueprint $table) {
            $table->id();
            $table->text('api_key');
            $table->text('auth_token');
            $table->string('currency_rate')->default(1);
            $table->string('account_mode')->default('Sandbox');
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
        Schema::dropIfExists('instamojo_payments');
    }
}
