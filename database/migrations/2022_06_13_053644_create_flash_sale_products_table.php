<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFlashSaleProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flash_sale_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('flash_sale_id');
            $table->integer('product_id');
            $table->integer('status')->default(1);
            $table->integer('show_homepage')->default(0);
            $table->timestamps();

            $table->foreign('flash_sale_id')->references('id')->on('flash_sales')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('flash_sale_products');
    }
}
