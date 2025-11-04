<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCookieConsentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cookie_consents', function (Blueprint $table) {
            $table->id();
            $table->integer('status')->default(1);
            $table->string('border')->nullable();
            $table->string('corners')->nullable();
            $table->string('background_color')->nullable();
            $table->string('text_color')->nullable();
            $table->string('border_color')->nullable();
            $table->string('btn_bg_color')->nullable();
            $table->string('btn_text_color')->nullable();
            $table->text('message')->nullable();
            $table->string('link_text')->nullable();
            $table->string('btn_text')->nullable();
            $table->string('link')->nullable();
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
        Schema::dropIfExists('cookie_consents');
    }
}
