<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGenderStreetHouseNoToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('email');
            $table->string('street')->nullable()->after('gender');
            $table->string('house_no')->nullable()->after('street');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
              $table->dropColumn(['gender', 'street', 'house_no']);
        });
    }
}
