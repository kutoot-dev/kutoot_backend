<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('short_name');
            $table->string('slug');
            $table->string('thumb_image');
            $table->integer('user_id')->default(0);
            $table->integer('category_id');
            $table->integer('sub_category_id');
            $table->integer('child_category_id');
            $table->integer('brand_id');
            $table->integer('qty');
            $table->text('short_description');
            $table->longText('long_description');
            $table->string('video_link')->nullable();
            $table->string('sku');
            $table->text('seo_title');
            $table->text('seo_description');
            $table->double('price');
            $table->double('offer_price')->nullable();
            $table->date('offer_start_date')->nullable();
            $table->date('offer_end_date')->nullable();
            $table->tinyInteger('is_cash_delivery')->default(0);
            $table->tinyInteger('is_return')->default(0);
            $table->integer('return_policy_id')->nullable();
            $table->tinyInteger('is_warranty')->default(0);
            $table->tinyInteger('show_homepage')->default(0);
            $table->tinyInteger('is_undefine')->default(0);
            $table->tinyInteger('is_featured')->default(0);
            $table->tinyInteger('new_product')->default(0);
            $table->tinyInteger('is_top')->default(0);
            $table->tinyInteger('is_best')->default(0);
            $table->tinyInteger('is_flash_deal')->default(0);
            $table->tinyInteger('buyone_getone')->default(0);
            $table->tinyInteger('status')->default(0);
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
        Schema::dropIfExists('products');
    }
}
