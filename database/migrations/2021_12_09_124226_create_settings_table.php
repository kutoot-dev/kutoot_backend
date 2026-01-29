<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            $table->integer('enable_user_register')->default(1);
            $table->integer('enable_multivendor')->default(1);
            $table->integer('enable_subscription_notify')->default(1);
            $table->integer('enable_save_contact_message')->default(1);
            $table->string('text_direction')->default('LTR');
            $table->string('timezone')->nullable();
            $table->string('sidebar_lg_header')->nullable();
            $table->string('sidebar_sm_header')->nullable();
            $table->string('featured_category_banner')->nullable();
            $table->string('current_version')->default('7.7');
            $table->double('tax')->default(0);
            $table->integer('currency_id')->nullable();
            $table->json('home_section_title')->nullable();
            $table->json('homepage_section_title')->nullable();
            $table->string('popular_category_banner')->nullable();
            $table->string('contact_email')->nullable();

            // Contact Information
            $table->string('topbar_phone')->nullable();
            $table->string('topbar_email')->nullable();

            // UI Configuration
            $table->integer('show_product_progressbar')->default(1);
            $table->string('phone_number_required')->nullable();
            $table->string('default_phone_code')->nullable();

            // Theme Configuration
            $table->string('theme_one')->nullable();
            $table->string('theme_two')->nullable();

            // Currency Display (for frontend)
            $table->string('currency_icon')->nullable();
            $table->string('currency_name')->nullable();

            // Content Fields
            $table->longText('seller_condition')->nullable();

            // Image Paths
            $table->string('empty_cart')->nullable();
            $table->string('empty_wishlist')->nullable();
            $table->string('change_password_image')->nullable();
            $table->string('become_seller_avatar')->nullable();
            $table->string('become_seller_banner')->nullable();
            $table->string('login_image')->nullable();
            $table->string('error_page')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
