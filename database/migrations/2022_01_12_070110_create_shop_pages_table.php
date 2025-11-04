<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop_pages', function (Blueprint $table) {
            $table->id();
            $table->string('header_one')->nullable();
            $table->string('header_two')->nullable();
            $table->string('title_one')->nullable();
            $table->string('title_two')->nullable();
            $table->string('banner')->nullable();
            $table->string('link')->nullable();
            $table->string('button_text')->nullable();
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
        Schema::dropIfExists('shop_pages');
    }
}
