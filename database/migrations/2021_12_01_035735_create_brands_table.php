<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBrandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('seller_id')->nullable();
            $table->string('name');
            $table->string('slug');
            $table->string('logo');
            $table->integer('status')->default(0);
            $table->tinyInteger('approval_status')->default(0);
            $table->integer('is_featured')->default(0);
            $table->integer('is_top')->default(0);
            $table->integer('is_popular')->default(0);
            $table->integer('is_trending')->default(0);
            $table->timestamps();

            // Note: Foreign key to vendors table removed - vendors table created after brands
            // Use index instead for query optimization
            $table->index('seller_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('brands');
    }
}
