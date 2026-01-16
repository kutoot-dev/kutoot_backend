<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopImagesTable extends Migration
{
    public function up()
    {
        Schema::create('shop_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id');
            $table->string('image_url');
            $table->timestamps();

            $table
                ->foreign('shop_id')
                ->references('id')
                ->on('shops')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('shop_images');
    }
}


