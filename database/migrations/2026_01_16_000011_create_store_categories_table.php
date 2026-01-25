<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoreCategoriesTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('store_categories')) {
            return;
        }

        Schema::create('store_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('image')->nullable();
            $table->string('icon')->nullable();
            $table->integer('serial')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('store_categories');
    }
}


