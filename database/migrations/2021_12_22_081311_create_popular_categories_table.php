<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePopularCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('popular_categories', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->integer('first_category_id')->default(0);
            $table->integer('first_sub_category_id')->default(0);
            $table->integer('first_child_category_id')->default(0);
            $table->integer('second_category_id')->default(0);
            $table->integer('second_sub_category_id')->default(0);
            $table->integer('second_child_category_id')->default(0);
            $table->integer('third_category_id')->default(0);
            $table->integer('third_sub_category_id')->default(0);
            $table->integer('third_child_category_id')->default(0);
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
        Schema::dropIfExists('popular_categories');
    }
}
