<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Per-store sale-return window (configured by the super admin).
 *
 * sell_return_period_days = number of days after the purchase date
 * within which a customer may return a sale at the store that made
 * it. NULL (or 0) means no time limit — the pre-existing behaviour —
 * so every current store keeps working unchanged until a limit is
 * set. Stored per business because each store is its own business in
 * this chain and policies can differ by state/store.
 */
return new class extends Migration
{
    public function up()
    {
        Schema::table('business', function (Blueprint $table) {
            $table->unsignedSmallInteger('sell_return_period_days')->nullable()->after('enable_sub_units');
        });
    }

    public function down()
    {
        Schema::table('business', function (Blueprint $table) {
            $table->dropColumn('sell_return_period_days');
        });
    }
};
