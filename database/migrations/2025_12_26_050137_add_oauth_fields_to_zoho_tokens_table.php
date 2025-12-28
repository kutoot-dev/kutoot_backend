<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('zoho_tokens', function (Blueprint $table) {

            if (!Schema::hasColumn('zoho_tokens', 'access_token')) {
                $table->string('access_token')->after('id');
            }

            if (!Schema::hasColumn('zoho_tokens', 'refresh_token')) {
                $table->string('refresh_token')->after('access_token');
            }

            if (!Schema::hasColumn('zoho_tokens', 'expires_at')) {
                $table->timestamp('expires_at')->after('refresh_token');
            }
        });
    }

    public function down()
    {
        Schema::table('zoho_tokens', function (Blueprint $table) {
            if (Schema::hasColumn('zoho_tokens', 'access_token')) {
                $table->dropColumn('access_token');
            }

            if (Schema::hasColumn('zoho_tokens', 'refresh_token')) {
                $table->dropColumn('refresh_token');
            }

            if (Schema::hasColumn('zoho_tokens', 'expires_at')) {
                $table->dropColumn('expires_at');
            }
        });
    }
};
