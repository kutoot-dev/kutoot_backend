<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablePrizewinnerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('table_prizewinner', function (Blueprint $table) {
            $table->id();
            $table->integer('camp_id');
            $table->integer('purchased_camp_id')->nullable();
            $table->integer('coupon_id')->nullable();
            $table->string('coupon_number');
            $table->integer('user_id')->nullable();
            $table->integer('is_claimed')->default(0);
            $table->text('prize_details')->nullable();
            $table->dateTime('announcing_date')->nullable();
            $table->integer('prize_id')->nullable();
            $table->tinyInteger('status')->default(1);
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
        Schema::dropIfExists('table_prizewinner');
    }
}
