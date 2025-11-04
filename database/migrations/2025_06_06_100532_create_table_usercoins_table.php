<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableUsercoinsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('table_usercoins', function (Blueprint $table) {
            $table->id();
            $table->integer('purchased_camp_id')->nullable();
            $table->integer('order_id')->nullable();
            $table->integer('user_id');
            $table->string('type')->index();
            $table->integer('coins')->default(0);
            $table->dateTime('coupon_expires')->nullable();
            $table->integer('status')->default(1);
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
        Schema::dropIfExists('table_usercoins');
    }
}
