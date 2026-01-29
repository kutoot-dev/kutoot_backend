<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Consolidates shops + admin_shop_commission_discounts into seller_applications
     */
    public function up(): void
    {
        // Step 1: Add missing columns to seller_applications
        Schema::table('seller_applications', function (Blueprint $table) {
            // From Shop table
            $table->string('shop_code', 255)->nullable()->unique()->after('application_id');
            $table->string('owner_name', 255)->nullable()->after('store_name');
            $table->string('google_map_url', 2048)->nullable()->after('lng');
            $table->json('tags')->nullable()->after('google_map_url');
            $table->string('razorpay_account_id', 255)->nullable()->after('upi_id');

            // From AdminShopCommissionDiscount table
            $table->integer('total_ratings')->default(0)->after('no_of_ratings');
            $table->boolean('is_active')->default(true)->after('total_ratings');
            $table->boolean('is_featured')->default(false)->after('is_active');
            $table->string('offer_tag', 255)->nullable()->after('is_featured');
            $table->date('last_updated_on')->nullable()->after('offer_tag');
        });

        // Step 2: Migrate data from shops to seller_applications
        DB::statement("
            UPDATE seller_applications sa
            JOIN store_sellers s ON sa.seller_id = s.id
            JOIN shops sh ON sh.seller_id = s.id
            SET
                sa.shop_code = sh.shop_code,
                sa.owner_name = sh.owner_name,
                sa.google_map_url = sh.google_map_url,
                sa.tags = sh.tags,
                sa.razorpay_account_id = sh.razorpay_account_id
            WHERE sa.status = 'APPROVED'
        ");

        // Step 3: Migrate data from admin_shop_commission_discounts
        DB::statement("
            UPDATE seller_applications sa
            JOIN store_sellers s ON sa.seller_id = s.id
            JOIN shops sh ON sh.seller_id = s.id
            JOIN admin_shop_commission_discounts ascd ON ascd.shop_id = sh.id
            SET
                sa.commission_percent = ascd.commission_percent,
                sa.discount_percent = ascd.discount_percent,
                sa.min_bill_amount = ascd.minimum_bill_amount,
                sa.rating = ascd.rating,
                sa.total_ratings = ascd.total_ratings,
                sa.is_active = ascd.is_active,
                sa.is_featured = ascd.is_featured,
                sa.offer_tag = ascd.offer_tag,
                sa.last_updated_on = ascd.last_updated_on
            WHERE sa.status = 'APPROVED'
        ");

        // Step 4: Add seller_application_id to related tables
        Schema::table('shop_images', function (Blueprint $table) {
            $table->unsignedBigInteger('seller_application_id')->nullable()->after('shop_id');
        });

        Schema::table('shop_visitors', function (Blueprint $table) {
            $table->unsignedBigInteger('seller_application_id')->nullable()->after('shop_id');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('seller_application_id')->nullable()->after('shop_id');
        });

        // Step 5: Migrate FK data for shop_images
        DB::statement("
            UPDATE shop_images si
            JOIN shops sh ON si.shop_id = sh.id
            JOIN store_sellers s ON sh.seller_id = s.id
            JOIN seller_applications sa ON sa.seller_id = s.id
            SET si.seller_application_id = sa.id
        ");

        // Step 6: Migrate FK data for shop_visitors
        DB::statement("
            UPDATE shop_visitors sv
            JOIN shops sh ON sv.shop_id = sh.id
            JOIN store_sellers s ON sh.seller_id = s.id
            JOIN seller_applications sa ON sa.seller_id = s.id
            SET sv.seller_application_id = sa.id
        ");

        // Step 7: Migrate FK data for transactions
        DB::statement("
            UPDATE transactions t
            JOIN shops sh ON t.shop_id = sh.id
            JOIN store_sellers s ON sh.seller_id = s.id
            JOIN seller_applications sa ON sa.seller_id = s.id
            SET t.seller_application_id = sa.id
        ");

        // Step 8: Add foreign key constraints
        Schema::table('shop_images', function (Blueprint $table) {
            $table->foreign('seller_application_id')
                ->references('id')
                ->on('seller_applications')
                ->onDelete('cascade');
        });

        Schema::table('shop_visitors', function (Blueprint $table) {
            $table->foreign('seller_application_id')
                ->references('id')
                ->on('seller_applications')
                ->onDelete('cascade');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->foreign('seller_application_id')
                ->references('id')
                ->on('seller_applications')
                ->onDelete('cascade');
        });

        // Step 9: Drop old shop_id foreign keys and columns
        Schema::table('shop_images', function (Blueprint $table) {
            $table->dropForeign(['shop_id']);
            $table->dropColumn('shop_id');
        });

        Schema::table('shop_visitors', function (Blueprint $table) {
            $table->dropForeign(['shop_id']);
            $table->dropColumn('shop_id');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['shop_id']);
            $table->dropColumn('shop_id');
        });

        // Step 10: Drop deprecated tables
        Schema::dropIfExists('admin_shop_commission_discounts');
        Schema::dropIfExists('shops');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate shops table
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('seller_id');
            $table->string('razorpay_account_id')->nullable();
            $table->string('shop_code')->unique();
            $table->string('shop_name');
            $table->string('category')->nullable();
            $table->string('owner_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('gst_number')->nullable();
            $table->string('address')->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->unsignedBigInteger('state_id')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->string('google_map_url', 2048)->nullable();
            $table->double('location_lat', 10, 7)->nullable();
            $table->double('location_lng', 10, 7)->nullable();
            $table->decimal('min_bill_amount', 10, 2)->default(0);
            $table->json('tags')->nullable();
            $table->timestamps();

            $table->foreign('seller_id')->references('id')->on('store_sellers')->onDelete('cascade');
        });

        // Recreate admin_shop_commission_discounts table
        Schema::create('admin_shop_commission_discounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->nullable()->unique();
            $table->unsignedInteger('commission_percent')->default(0);
            $table->unsignedInteger('discount_percent')->default(0);
            $table->decimal('minimum_bill_amount', 12, 2)->default(0);
            $table->decimal('rating', 2, 1)->default(0);
            $table->integer('total_ratings')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->string('offer_tag')->nullable();
            $table->date('last_updated_on')->nullable();
            $table->timestamps();

            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
        });

        // Add shop_id back to related tables
        Schema::table('shop_images', function (Blueprint $table) {
            $table->unsignedBigInteger('shop_id')->nullable()->after('id');
            $table->dropForeign(['seller_application_id']);
            $table->dropColumn('seller_application_id');
        });

        Schema::table('shop_visitors', function (Blueprint $table) {
            $table->unsignedBigInteger('shop_id')->nullable()->after('id');
            $table->dropForeign(['seller_application_id']);
            $table->dropColumn('seller_application_id');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('shop_id')->nullable()->after('id');
            $table->dropForeign(['seller_application_id']);
            $table->dropColumn('seller_application_id');
        });

        // Remove new columns from seller_applications
        Schema::table('seller_applications', function (Blueprint $table) {
            $table->dropColumn([
                'shop_code',
                'owner_name',
                'google_map_url',
                'tags',
                'razorpay_account_id',
                'total_ratings',
                'is_active',
                'is_featured',
                'offer_tag',
                'last_updated_on',
            ]);
        });
    }
};
