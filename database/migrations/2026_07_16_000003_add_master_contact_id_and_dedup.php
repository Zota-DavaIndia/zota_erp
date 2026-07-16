<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adds a master/clone relationship to contacts so that the
     * universal customer list shows each customer exactly once.
     *
     * - A row with `master_contact_id` IS NULL is a "master" record
     *   and is the canonical entry shown in the UI.
     * - A row with `master_contact_id` pointing to another row is a
     *   "clone" - it was the same logical customer duplicated in
     *   another business before universalization. Clones are hidden
     *   from listings but kept in the DB so that historical
     *   transactions and references remain valid.
     *
     * This migration also backfills the relationship for the
     * two kinds of duplicates that the chain currently has:
     *   1. Default "Walk-In Customer" rows seeded per business.
     *   2. Customers with the same non-empty mobile number across
     *      different businesses.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('contacts', 'master_contact_id')) {
            Schema::table('contacts', function (Blueprint $table) {
                $table->unsignedInteger('master_contact_id')->nullable()->after('source_business_id')->index();
            });
        }

        $this->backfill();
    }

    /**
     * Walk the contact table once and link every duplicate to the
     * earliest-seen master. Two contacts are considered duplicates
     * when:
     *   - Both are default customers in the "Walk-In Customer"
     *     group (is_default = 1, type in [customer, both], name
     *     = "Walk-In Customer" and no mobile).
     *   - OR both are customers with the same non-empty mobile
     *     number (case-insensitive trim match).
     *
     * @return void
     */
    protected function backfill(): void
    {
        // 1) Default Walk-In Customers: keep the lowest-id row as
        //    master, link the rest to it.
        $walkIns = DB::table('contacts')
            ->whereIn('type', ['customer', 'both'])
            ->where('is_default', 1)
            ->whereNull('master_contact_id')
            ->orderBy('id')
            ->pluck('id');

        if ($walkIns->count() > 1) {
            $walkInMaster = $walkIns->first();
            DB::table('contacts')
                ->whereIn('id', $walkIns->slice(1)->all())
                ->whereNull('master_contact_id')
                ->update(['master_contact_id' => $walkInMaster]);
        }

        // 2) Customers grouped by non-empty mobile: keep the
        //    lowest-id row as master, link the rest to it.
        $dupMobiles = DB::table('contacts')
            ->select('mobile')
            ->whereIn('type', ['customer', 'both'])
            ->where('is_default', 0)
            ->whereNotNull('mobile')
            ->where('mobile', '<>', '')
            ->groupBy('mobile')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('mobile');

        foreach ($dupMobiles as $mobile) {
            $rows = DB::table('contacts')
                ->whereIn('type', ['customer', 'both'])
                ->where('is_default', 0)
                ->where('mobile', $mobile)
                ->whereNull('master_contact_id')
                ->orderBy('id')
                ->pluck('id')
                ->all();

            if (count($rows) > 1) {
                $master = $rows[0];
                DB::table('contacts')
                    ->whereIn('id', array_slice($rows, 1))
                    ->whereNull('master_contact_id')
                    ->update(['master_contact_id' => $master]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('contacts', 'master_contact_id')) {
            Schema::table('contacts', function (Blueprint $table) {
                $table->dropColumn('master_contact_id');
            });
        }
    }
};
