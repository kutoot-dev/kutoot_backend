<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderProductVariantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_product_variants', function (Blueprint $table) {
            $table->id();
            $table->integer('order_product_id');
            $table->integer('product_id');
            $table->string('variant_name')->nullable();
            $table->string('variant_value')->nullable();
            $table->double('variant_price')->default(0);
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
        Schema::dropIfExists('order_product_variants');
    }
}
