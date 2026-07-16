<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('products', 'is_master_product')) {
            Schema::table('products', function (Blueprint $table) {
                $table->boolean('is_master_product')->default(0);
            });
        }
        if (!Schema::hasColumn('products', 'superadmin_product_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->unsignedInteger('superadmin_product_id')->nullable()->index();
            });
        }
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['is_master_product', 'superadmin_product_id']);
        });
    }
};
