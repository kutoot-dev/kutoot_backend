<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->integer('admin_id');
            $table->string('title');
            $table->string('slug');
            $table->integer('blog_category_id');
            $table->string('image');
            $table->longText('description');
            $table->integer('views')->default(0);
            $table->string('seo_title');
            $table->string('seo_description');
            $table->integer('status')->default(0);
            $table->integer('show_homepage')->default(0);
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
        Schema::dropIfExists('blogs');
    }
}
