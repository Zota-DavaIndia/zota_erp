<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adds a self-referential pointer so business copies of variations
     * can be matched to their master variation row by id, instead of by
     * name (which is not unique and was unreliable).
     *
     * Intentionally NO foreign key: a business copy's master_variation_id
     * may point to a variation row owned by the master product in a
     * different business. Adding a FK could conflict with cross-business
     * data and would complicate the existing legacy schema.
     */
    public function up()
    {
        if (!Schema::hasColumn('variations', 'master_variation_id')) {
            Schema::table('variations', function (Blueprint $table) {
                $table->unsignedBigInteger('master_variation_id')->nullable()->after('product_variation_id');
                $table->index('master_variation_id');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('variations', 'master_variation_id')) {
            Schema::table('variations', function (Blueprint $table) {
                $table->dropIndex(['master_variation_id']);
                $table->dropColumn('master_variation_id');
            });
        }
    }
};
