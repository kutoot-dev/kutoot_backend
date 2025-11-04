<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExpiredDateToAnnouncementModalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('announcement_modals', function (Blueprint $table) {
             $table->date('expired_date')->nullable()->after('image'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('announcement_modals', function (Blueprint $table) {
            $table->dropColumn('expired_date');
        });
    }
}
