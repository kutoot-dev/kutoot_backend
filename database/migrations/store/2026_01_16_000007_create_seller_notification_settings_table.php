<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSellerNotificationSettingsTable extends Migration
{
    public function up()
    {
        Schema::create('seller_notification_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('seller_id')->unique();
            $table->boolean('enabled')->default(true);
            $table->boolean('email')->default(true);
            $table->boolean('sms')->default(false);
            $table->boolean('whatsapp')->default(true);
            $table->timestamps();

            $table
                ->foreign('seller_id')
                ->references('id')
                ->on('store_sellers')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('seller_notification_settings');
    }
}


