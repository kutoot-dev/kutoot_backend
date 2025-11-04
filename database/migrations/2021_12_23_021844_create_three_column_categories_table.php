<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateThreeColumnCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('three_column_categories', function (Blueprint $table) {
            $table->id();
            $table->integer('category_id_one')->default(0);
            $table->integer('sub_category_id_one')->default(0);
            $table->integer('child_category_id_one')->default(0);
            $table->integer('category_id_two')->default(0);
            $table->integer('sub_category_id_two')->default(0);
            $table->integer('child_category_id_two')->default(0);
            $table->integer('category_id_three')->default(0);
            $table->integer('sub_category_id_three')->default(0);
            $table->integer('child_category_id_three')->default(0);
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
        Schema::dropIfExists('three_column_categories');
    }
}
