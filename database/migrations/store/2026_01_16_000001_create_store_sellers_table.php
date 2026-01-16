<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoreSellersTable extends Migration
{
    public function up()
    {
        Schema::create('store_sellers', function (Blueprint $table) {
            $table->id();
            $table->string('seller_code')->unique();
            $table->string('username')->unique();
            $table->string('password');
            $table->string('owner_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->unsignedTinyInteger('status')->default(1); // 1=ACTIVE, 0=INACTIVE
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('store_sellers');
    }
}


