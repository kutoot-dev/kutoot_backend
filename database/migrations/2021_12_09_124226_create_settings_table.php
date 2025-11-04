<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            $table->integer('enable_user_register')->default(1);
            $table->integer('enable_multivendor')->default(1);
            $table->integer('enable_subscription_notify')->default(1);
            $table->integer('enable_save_contact_message')->default(1);
            $table->string('text_direction')->default('LTR');
            $table->string('timezone')->nullable();
            $table->string('sidebar_lg_header')->nullable();
            $table->string('sidebar_sm_header')->nullable();
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
        Schema::dropIfExists('settings');
    }
}
