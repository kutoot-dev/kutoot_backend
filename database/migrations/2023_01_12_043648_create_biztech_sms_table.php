<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBiztechSmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('biztech_sms', function (Blueprint $table) {
            $table->id();
            $table->text('api_key');
            $table->text('client_id');
            $table->string('sender_id');
            $table->integer('enable_register_sms')->default(0);
            $table->integer('enable_reset_pass_sms')->default(0);
            $table->integer('enable_order_confirmation_sms')->default(0);
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
        Schema::dropIfExists('biztech_sms');
    }
}
