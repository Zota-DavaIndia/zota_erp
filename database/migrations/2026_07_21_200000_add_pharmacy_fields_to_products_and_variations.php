<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('hsn_code', 20)->nullable()->after('sku');
            $table->string('drug_schedule', 10)->nullable()->after('hsn_code');
            $table->boolean('prescription_required')->default(0)->after('drug_schedule');
            $table->string('dosage_form', 50)->nullable()->after('prescription_required');
            $table->string('storage_condition', 100)->nullable()->after('dosage_form');
        });

        Schema::table('variations', function (Blueprint $table) {
            $table->decimal('mrp_inc_tax', 22, 4)->default(0)->after('sell_price_inc_tax');
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['hsn_code', 'drug_schedule', 'prescription_required', 'dosage_form', 'storage_condition']);
        });

        Schema::table('variations', function (Blueprint $table) {
            $table->dropColumn('mrp_inc_tax');
        });
    }
};
