<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCurrencyIdToMyfatoorahPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('myfatoorah_payments', function (Blueprint $table) {
            $table->integer('currency_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('myfatoorah_payments', function (Blueprint $table) {
            $table->dropColumn('currency_id');
        });
    }
}
