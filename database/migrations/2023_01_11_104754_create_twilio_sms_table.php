<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTwilioSmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('twilio_sms', function (Blueprint $table) {
            $table->id();
            $table->text('account_sid');
            $table->text('auth_token');
            $table->string('twilio_phone_number');
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
        Schema::dropIfExists('twilio_sms');
    }
}
