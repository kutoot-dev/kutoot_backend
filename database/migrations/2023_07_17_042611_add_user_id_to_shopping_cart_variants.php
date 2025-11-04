<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdToShoppingCartVariants extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shopping_cart_variants', function (Blueprint $table) {
            $table->integer('user_id')->default(0);
            $table->integer('product_id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shopping_cart_variants', function (Blueprint $table) {
            $table->dropColumn('user_id');
            $table->dropColumn('product_id');
        });
    }
}
