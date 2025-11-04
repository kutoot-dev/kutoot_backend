<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMegaMenuSubCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mega_menu_sub_categories', function (Blueprint $table) {
            $table->id();
            $table->integer('mega_menu_category_id');
            $table->integer('status')->default(0);
            $table->integer('serial')->default(0);
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
        Schema::dropIfExists('mega_menu_sub_categories');
    }
}
