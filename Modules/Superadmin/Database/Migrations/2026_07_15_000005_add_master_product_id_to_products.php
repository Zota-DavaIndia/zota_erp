<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Re-add column to track which master product a business copy was synced from.
        // Points to another row in the same `products` table (where is_master_product=1).
        if (!Schema::hasColumn('products', 'master_product_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->unsignedBigInteger('master_product_id')->nullable()->after('is_master_product');
                $table->index('master_product_id');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('products', 'master_product_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropIndex(['master_product_id']);
                $table->dropColumn('master_product_id');
            });
        }
    }
};
