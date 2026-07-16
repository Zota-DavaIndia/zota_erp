<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds an `is_global` flag to the `contacts` table. When set to 1,
     * the contact is shared across all businesses (chain-wide), and
     * will be visible/usable from every store in the system. The
     * `business_id` is still kept as the business that originally
     * created the contact (for FK integrity and audit).
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('contacts', 'is_global')) {
            Schema::table('contacts', function (Blueprint $table) {
                $table->boolean('is_global')->default(0)->after('is_default')->index();
            });
        }

        if (! Schema::hasColumn('contacts', 'source_business_id')) {
            Schema::table('contacts', function (Blueprint $table) {
                $table->unsignedInteger('source_business_id')->nullable()->after('is_global')->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('contacts', 'is_global')) {
            Schema::table('contacts', function (Blueprint $table) {
                $table->dropColumn('is_global');
            });
        }

        if (Schema::hasColumn('contacts', 'source_business_id')) {
            Schema::table('contacts', function (Blueprint $table) {
                $table->dropColumn('source_business_id');
            });
        }
    }
};
