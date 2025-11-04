<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePusherCredentailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pusher_credentails', function (Blueprint $table) {
            $table->id();
            $table->string('app_id');
            $table->string('app_key');
            $table->string('app_secret');
            $table->string('app_cluster');
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
        Schema::dropIfExists('pusher_credentails');
    }
}
