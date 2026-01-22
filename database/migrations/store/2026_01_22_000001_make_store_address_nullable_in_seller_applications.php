<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class MakeStoreAddressNullableInSellerApplications extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE `seller_applications` MODIFY `store_address` VARCHAR(255) NULL');
        DB::statement('ALTER TABLE `seller_applications` MODIFY `owner_email` VARCHAR(255) NULL');
    }

    public function down()
    {
        DB::statement('ALTER TABLE `seller_applications` MODIFY `store_address` VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE `seller_applications` MODIFY `owner_email` VARCHAR(255) NOT NULL');
    }
}

