<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('variation_location_details', function (Blueprint $table) {
            $table->decimal('min_quantity', 22, 4)->default(0)->after('qty_available');
            $table->decimal('max_quantity', 22, 4)->default(0)->after('min_quantity');
            $table->string('movement_tag', 10)->nullable()->after('max_quantity');
            $table->enum('min_max_source', ['manual', 'auto'])->default('manual')->after('movement_tag');
            $table->timestamp('last_auto_update_at')->nullable()->after('min_max_source');
        });
    }

    public function down()
    {
        Schema::table('variation_location_details', function (Blueprint $table) {
            $table->dropColumn(['min_quantity', 'max_quantity', 'movement_tag', 'min_max_source', 'last_auto_update_at']);
        });
    }
};
