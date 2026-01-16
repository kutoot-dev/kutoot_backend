<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopsTable extends Migration
{
    public function up()
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('seller_id');
            $table->string('shop_code')->unique();
            $table->string('shop_name');
            $table->string('category')->nullable();
            $table->string('owner_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('gst_number')->nullable();
            $table->string('address')->nullable();
            $table->string('google_map_url', 2048)->nullable();
            $table->double('location_lat', 10, 7)->nullable();
            $table->double('location_lng', 10, 7)->nullable();
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
        Schema::dropIfExists('shops');
    }
}


