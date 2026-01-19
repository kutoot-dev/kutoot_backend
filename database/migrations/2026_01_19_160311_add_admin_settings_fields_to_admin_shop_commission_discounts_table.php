<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdminSettingsFieldsToAdminShopCommissionDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('admin_shop_commission_discounts', function (Blueprint $table) {
            $table->decimal('rating', 2, 1)->default(0.0)->after('minimum_bill_amount'); // e.g., 4.5
            $table->integer('total_ratings')->default(0)->after('rating');
            $table->boolean('is_active')->default(true)->after('total_ratings');
            $table->boolean('is_featured')->default(false)->after('is_active');
            $table->string('offer_tag')->nullable()->after('is_featured'); // e.g., "Hot Deal"
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('admin_shop_commission_discounts', function (Blueprint $table) {
            $table->dropColumn(['rating', 'total_ratings', 'is_active', 'is_featured', 'offer_tag']);
        });
    }
}
