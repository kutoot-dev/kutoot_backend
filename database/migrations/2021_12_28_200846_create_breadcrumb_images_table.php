<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBreadcrumbImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('breadcrumb_images', function (Blueprint $table) {
            $table->id();
            $table->string('location')->nullable();
            $table->integer('image_type')->default(1);
            $table->string('image')->nullable();
            $table->string('title')->nullable();
            $table->string('header')->nullable();
            $table->text('description')->nullable();
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
        Schema::dropIfExists('breadcrumb_images');
    }
}
