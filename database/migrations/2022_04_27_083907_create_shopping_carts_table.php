<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShoppingCartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shopping_carts', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('product_id');
            $table->text('name')->nullable();
            $table->integer('qty');
            $table->double('price');
            $table->double('total_price')->nullable();
            $table->double('tax')->nullable();
            $table->double('coupon_price')->nullable();
            $table->integer('offer_type')->nullable();
            $table->text('image')->nullable();
            $table->text('slug')->nullable();
            $table->integer('reedem_coins')->default(0);
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
        Schema::dropIfExists('shopping_carts');
    }
}
