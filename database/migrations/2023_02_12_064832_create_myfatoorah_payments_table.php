<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMyfatoorahPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('myfatoorah_payments', function (Blueprint $table) {
            $table->id();
            $table->integer('status');
            $table->string('account_mode');
            $table->string('currency_code');
            $table->string('currency_rate');
            $table->text('api_key');
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
        Schema::dropIfExists('myfatoorah_payments');
    }
}
