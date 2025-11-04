<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSocialLoginInformationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('social_login_information', function (Blueprint $table) {
            $table->id();
            $table->integer('is_facebook')->default(0);
            $table->text('facebook_client_id')->nullable();
            $table->text('facebook_secret_id')->nullable();
            $table->integer('is_gmail')->default(0);
            $table->text('gmail_client_id')->nullable();
            $table->text('gmail_secret_id')->nullable();
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
        Schema::dropIfExists('social_login_information');
    }
}
