<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOwnerEmailToSellerApplicationsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('seller_applications', 'owner_email')) {
            Schema::table('seller_applications', function (Blueprint $table) {
                $table->string('owner_email')->nullable()->after('owner_mobile');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('seller_applications', 'owner_email')) {
            Schema::table('seller_applications', function (Blueprint $table) {
                $table->dropColumn('owner_email');
            });
        }
    }
}

