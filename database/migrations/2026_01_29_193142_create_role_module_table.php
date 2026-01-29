<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('role_module', function (Blueprint $table) {
            $table->id(); // bigint unsigned auto increment

            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('module_id');

            $table->timestamps();

            // Unique constraint (role_id + module_id)
            $table->unique(['role_id', 'module_id'], 'role_module_unique');

            // Foreign keys
            $table->foreign('role_id')
                  ->references('id')
                  ->on('roles')
                  ->onDelete('cascade');

            $table->foreign('module_id')
                  ->references('id')
                  ->on('modules')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('role_module');
    }
};
