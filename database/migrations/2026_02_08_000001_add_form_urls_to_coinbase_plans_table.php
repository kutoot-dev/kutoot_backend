<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFormUrlsToCoinbasePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('coinbase_plans', function (Blueprint $table) {
            $table->string('referral_form_url')->nullable()->after('point5');
            $table->string('task_form_url')->nullable()->after('referral_form_url');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('coinbase_plans', function (Blueprint $table) {
            $table->dropColumn(['referral_form_url', 'task_form_url']);
        });
    }
}
