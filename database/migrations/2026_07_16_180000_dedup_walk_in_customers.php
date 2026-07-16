<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Collapse every per-business "Walk-In Customer" row into a
     * single, chain-wide master record. The earlier dedup migration
     * (`2026_07_16_000003_add_master_contact_id_and_dedup`) only
     * fixed the duplicates that existed at the time it ran. New
     * businesses added after that migration each create their own
     * Walk-In Customer with `is_default = 1` and `master_contact_id
     * IS NULL`, so the customer list grew to 9+ rows of "Walk-In
     * Customer" with the same contact_id (CO0001) - one per store.
     *
     * This migration:
     *   1. Picks the lowest-id `is_default = 1` customer as the
     *      master.
     *   2. Re-points any sales / purchases / transactions whose
     *      `contact_id` is one of the duplicate rows to the master
     *      id, so historical data stays attached to a valid record.
     *   3. Soft-links the duplicates by setting `master_contact_id`
     *      and `is_global = 1` so they are hidden from the universal
     *      customer list but kept for FK safety.
     *   4. Marks the master as `is_global = 1` and ensures it stays
     *      the visible "default" walk-in.
     *
     * Safe to re-run: only rows that are still orphans
     * (master_contact_id IS NULL) are touched.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('contacts', 'master_contact_id')
            || ! Schema::hasColumn('contacts', 'is_global')) {
            // Prerequisites from earlier migrations are missing -
            // skip silently so this remains a no-op on partial
            // installations.
            return;
        }

        $walkIns = DB::table('contacts')
            ->whereIn('type', ['customer', 'both'])
            ->where('is_default', 1)
            ->whereNull('master_contact_id')
            ->orderBy('id')
            ->pluck('id')
            ->all();

        if (count($walkIns) <= 1) {
            // Nothing to dedup - either zero or one walk-in row
            // already. Just make sure the master is global.
            if (count($walkIns) === 1) {
                DB::table('contacts')
                    ->where('id', $walkIns[0])
                    ->update(['is_global' => 1, 'is_default' => 1]);
            }
            return;
        }

        $masterId = (int) $walkIns[0];
        $duplicateIds = array_map('intval', array_slice($walkIns, 1));

        // 1) Re-point every FK from the duplicates to the master so
        //    historical transactions / purchases stay valid.
        $tables = [
            'transactions'      => 'contact_id',
            'transaction_payments' => 'payment_for', // may not exist
            'customer_groups'    => null,            // not a FK
        ];

        if (Schema::hasTable('transactions')) {
            DB::table('transactions')
                ->whereIn('contact_id', $duplicateIds)
                ->update(['contact_id' => $masterId]);
        }

        if (Schema::hasTable('transaction_payments')
            && Schema::hasColumn('transaction_payments', 'payment_for')) {
            DB::table('transaction_payments')
                ->whereIn('payment_for', $duplicateIds)
                ->update(['payment_for' => $masterId]);
        }

        // 2) Soft-link the duplicates: mark them as global, attach
        //    them to the master, and stop them from being the
        //    default for any business.
        DB::table('contacts')
            ->whereIn('id', $duplicateIds)
            ->update([
                'master_contact_id' => $masterId,
                'is_default'        => 0,
                'is_global'         => 1,
            ]);

        // 3) Promote the master.
        DB::table('contacts')
            ->where('id', $masterId)
            ->update([
                'is_default'        => 1,
                'is_global'         => 1,
                'master_contact_id' => null,
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * Unlinks the walk-in duplicates from the master. The
     * `is_global` / `is_default` flags are left as-is because we
     * cannot reliably reconstruct the original per-business state.
     *
     * @return void
     */
    public function down()
    {
        if (! Schema::hasColumn('contacts', 'master_contact_id')) {
            return;
        }

        $masterId = DB::table('contacts')
            ->whereIn('type', ['customer', 'both'])
            ->where('is_default', 1)
            ->whereNull('master_contact_id')
            ->orderBy('id')
            ->value('id');

        if (! $masterId) {
            return;
        }

        DB::table('contacts')
            ->where('master_contact_id', $masterId)
            ->whereIn('type', ['customer', 'both'])
            ->update(['master_contact_id' => null]);
    }
};
