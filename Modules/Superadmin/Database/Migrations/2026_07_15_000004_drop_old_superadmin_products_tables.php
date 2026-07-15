<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Drop the old superadmin_product_variations table first (has FK)
        Schema::dropIfExists('superadmin_product_variations');

        // Drop the old superadmin_products table
        Schema::dropIfExists('superadmin_products');

        // Remove superadmin_product_id column from products table
        if (Schema::hasColumn('products', 'superadmin_product_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropIndex(['superadmin_product_id']);
                $table->dropColumn('superadmin_product_id');
            });
        }
    }

    public function down()
    {
        // Re-add superadmin_product_id to products
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('superadmin_product_id')->nullable()->after('id');
            $table->index('superadmin_product_id');
        });

        // Re-create superadmin_products table
        Schema::create('superadmin_products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->default('single');
            $table->string('sku')->nullable();
            $table->string('barcode_type')->default('C128');
            $table->string('image')->nullable();
            $table->text('product_description')->nullable();
            $table->decimal('weight', 22, 4)->nullable();
            $table->boolean('enable_stock')->default(1);
            $table->decimal('alert_quantity', 22, 4)->nullable();
            $table->boolean('not_for_selling')->default(0);
            $table->string('tax_type')->nullable();
            $table->boolean('is_active')->default(1);
            $table->integer('created_by');
            $table->string('unit_name')->nullable();
            $table->string('unit_short_name')->nullable();
            $table->string('category_name')->nullable();
            $table->string('brand_name')->nullable();
            $table->timestamps();
        });

        // Re-create superadmin_product_variations table
        Schema::create('superadmin_product_variations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('superadmin_product_id');
            $table->string('name');
            $table->string('sub_sku')->nullable();
            $table->decimal('default_purchase_price', 22, 4)->default(0);
            $table->decimal('dpp_inc_tax', 22, 4)->default(0);
            $table->decimal('profit_percent', 22, 4)->default(0);
            $table->decimal('default_sell_price', 22, 4)->default(0);
            $table->decimal('sell_price_inc_tax', 22, 4)->default(0);
            $table->boolean('is_dummy')->default(0);
            $table->timestamps();
            $table->foreign('superadmin_product_id')->references('id')->on('superadmin_products')->onDelete('cascade');
        });
    }
};
