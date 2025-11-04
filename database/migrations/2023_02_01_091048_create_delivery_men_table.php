<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryMenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_men', function (Blueprint $table) {
            $table->id();
            $table->string('man_image')->nullable();
            $table->string('fname');
            $table->string('lname');
            $table->string('email')->unique();
            $table->string('man_type');
            $table->string('idn_type');
            $table->string('idn_num');
            $table->string('idn_image');
            $table->string('phone');
            $table->string('password');
            $table->tinyInteger('status')->deafult(0);
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
        Schema::dropIfExists('delivery_men');
    }
}
