<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoreBannersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_banners', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable()->comment('Desktop image path');
            $table->string('image_tablet')->nullable()->comment('Tablet image path');
            $table->string('image_mobile')->nullable()->comment('Mobile image path');
            $table->string('link')->nullable();
            $table->string('button_text')->nullable();
            $table->string('location')->nullable()->comment('Banner placement location');
            $table->integer('serial')->default(0)->comment('Display order');
            $table->tinyInteger('status')->default(1)->comment('1=active, 0=inactive');
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('location');
            $table->index(['status', 'location']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('store_banners');
    }
}
