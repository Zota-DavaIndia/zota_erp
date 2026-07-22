<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A management-assigned unique identifier for each store (business).
 * Entered manually by the super admin at store creation and editable
 * afterwards. Nullable (existing stores have none until set) and
 * uniquely indexed so no two stores can share the same number.
 */
return new class extends Migration
{
    public function up()
    {
        Schema::table('business', function (Blueprint $table) {
            $table->string('store_unique_number', 191)->nullable()->after('name');
            $table->unique('store_unique_number');
        });
    }

    public function down()
    {
        Schema::table('business', function (Blueprint $table) {
            $table->dropUnique(['store_unique_number']);
            $table->dropColumn('store_unique_number');
        });
    }
};
