<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImageToStoreCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('store_categories', function (Blueprint $table) {
            $table->string('image')->nullable()->after('name');
            $table->string('icon')->nullable()->after('image');
            $table->integer('serial')->default(0)->after('icon');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('store_categories', function (Blueprint $table) {
            $table->dropColumn(['image', 'icon', 'serial']);
        });
    }
}
