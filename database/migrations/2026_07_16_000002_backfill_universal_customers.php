<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Backfill: mark every existing customer as universal (is_global = 1)
     * and stamp source_business_id with the business that owns them.
     *
     * After this migration, every customer in the system is shared
     * across all businesses. This aligns with the "Universal Customer"
     * requirement: any customer entered in any store is visible in
     * every other store.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('contacts', 'is_global')
            && Schema::hasColumn('contacts', 'source_business_id')) {
            DB::table('contacts')
                ->whereIn('type', ['customer', 'both'])
                ->whereNull('deleted_at')
                ->update([
                    'is_global' => 1,
                ]);

            DB::table('contacts')
                ->whereIn('type', ['customer', 'both'])
                ->whereNull('source_business_id')
                ->whereNull('deleted_at')
                ->update([
                    'source_business_id' => DB::raw('business_id'),
                ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * Note: down() only clears source_business_id; it intentionally
     * leaves is_global=1 in place because that is the desired
     * permanent behavior. Reverting to per-business isolation is a
     * separate, larger migration if ever needed.
     *
     * @return void
     */
    public function down()
    {
        // No-op: we don't want to silently break the universal customer
        // feature by reverting this migration.
    }
};
