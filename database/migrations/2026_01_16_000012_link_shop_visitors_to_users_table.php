<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LinkShopVisitorsToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('shop_visitors', function (Blueprint $table) {
            if (!Schema::hasColumn('shop_visitors', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('shop_id');
                $table->index('user_id');
                $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            }
        });
    }

    public function down()
    {
        Schema::table('shop_visitors', function (Blueprint $table) {
            if (Schema::hasColumn('shop_visitors', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropIndex(['user_id']);
                $table->dropColumn('user_id');
            }
        });
    }
}


