<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Per-product transaction-context unit whitelists.
 *
 * products.sub_unit_ids already whitelists which units a product can
 * be transacted in, but it applies to BOTH selling and purchasing.
 * A pharmacy needs asymmetric rules: sell in Strip/Tablet only,
 * purchase in Baby Box only. These two columns narrow the whitelist
 * per context; NULL/empty means "no extra restriction" (falls back
 * to sub_unit_ids), keeping every existing product's behaviour
 * unchanged.
 */
return new class extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->text('sell_sub_unit_ids')->nullable()->after('default_purchase_sub_unit_id');
            $table->text('purchase_sub_unit_ids')->nullable()->after('sell_sub_unit_ids');
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['sell_sub_unit_ids', 'purchase_sub_unit_ids']);
        });
    }
};
