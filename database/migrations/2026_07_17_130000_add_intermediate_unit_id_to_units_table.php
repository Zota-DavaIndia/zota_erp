<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds intermediate_unit_id to the units table so the
     * "Define via intermediate unit" relationship (e.g.
     * 1 Baby Box = 10 Strips where 1 Strip = 10 Tablets)
     * can be remembered across edits. Without this column
     * the chain is lost on every re-edit, forcing the
     * admin to redefine the ratio.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('units', 'intermediate_unit_id')) {
            Schema::table('units', function (Blueprint $table) {
                $table->unsignedInteger('intermediate_unit_id')
                    ->nullable()
                    ->after('base_unit_multiplier');
                $table->index('intermediate_unit_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('units', 'intermediate_unit_id')) {
            Schema::table('units', function (Blueprint $table) {
                $table->dropIndex(['intermediate_unit_id']);
                $table->dropColumn('intermediate_unit_id');
            });
        }
    }
};
