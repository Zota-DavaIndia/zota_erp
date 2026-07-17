<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds two "preferred unit" pointers on the products table:
     *
     *   default_sell_sub_unit_id    — pre-selected at POS for this
     *                                  product (e.g. Strip when a
     *                                  medicine is mostly sold in
     *                                  strips but can also be sold
     *                                  loose in tablets).
     *   default_purchase_sub_unit_id — pre-selected at purchase entry
     *                                  for this product (e.g. Baby
     *                                  Box when the supplier always
     *                                  delivers in baby boxes).
     *
     * Both must belong to the product's sub_unit_ids list (or be the
     * product's own unit_id) — enforced at the controller level. The
     * base unit (lowest unit) is still the unit stock is tracked in;
     * these fields only affect which option is shown selected at
     * POS / purchase entry. The math in vld.qty_available and
     * variation prices is unchanged.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('products', 'default_sell_sub_unit_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->unsignedInteger('default_sell_sub_unit_id')
                    ->nullable()
                    ->after('unit_id');
                $table->index('default_sell_sub_unit_id');
            });
        }

        if (! Schema::hasColumn('products', 'default_purchase_sub_unit_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->unsignedInteger('default_purchase_sub_unit_id')
                    ->nullable()
                    ->after('default_sell_sub_unit_id');
                $table->index('default_purchase_sub_unit_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('products', 'default_purchase_sub_unit_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropIndex(['default_purchase_sub_unit_id']);
                $table->dropColumn('default_purchase_sub_unit_id');
            });
        }
        if (Schema::hasColumn('products', 'default_sell_sub_unit_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropIndex(['default_sell_sub_unit_id']);
                $table->dropColumn('default_sell_sub_unit_id');
            });
        }
    }
};
