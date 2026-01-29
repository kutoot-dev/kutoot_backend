<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymongoPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paymongo_payments', function (Blueprint $table) {
            $table->id();
            $table->string('public_key')->nullable();
            $table->string('secret_key')->nullable();
            $table->string('country_code')->nullable();
            $table->string('currency_code')->nullable();
            $table->unsignedBigInteger('currency_id')->nullable();
            $table->decimal('currency_rate', 10, 2)->default(1.00);
            $table->string('image')->nullable();
            $table->string('webhook_sig')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->timestamps();

            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('paymongo_payments');
    }
}
