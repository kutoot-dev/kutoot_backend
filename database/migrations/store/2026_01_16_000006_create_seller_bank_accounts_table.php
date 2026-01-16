<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSellerBankAccountsTable extends Migration
{
    public function up()
    {
        Schema::create('seller_bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('seller_id')->unique();
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('ifsc')->nullable();
            $table->string('upi_id')->nullable();
            $table->string('beneficiary_name')->nullable();
            $table->timestamps();

            $table
                ->foreign('seller_id')
                ->references('id')
                ->on('store_sellers')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('seller_bank_accounts');
    }
}


