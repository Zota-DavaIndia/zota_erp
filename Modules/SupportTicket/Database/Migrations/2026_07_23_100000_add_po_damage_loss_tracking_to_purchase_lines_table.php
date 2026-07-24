<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_lines', function (Blueprint $table) {
            // Cumulative damaged/lost quantity received against a PO line (mirrors po_quantity_purchased),
            // used to determine when a PO line is fully accounted for and to gate further GRN creation.
            $table->decimal('po_quantity_damaged', 22, 4)->default(0)->after('po_quantity_purchased');
            $table->decimal('po_quantity_lost', 22, 4)->default(0)->after('po_quantity_damaged');

            // Set on a resend GRN's line to trace which support ticket it fulfills.
            $table->integer('support_ticket_id')->unsigned()->nullable()->after('po_quantity_lost');
            $table->index('support_ticket_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_lines', function (Blueprint $table) {
            $table->dropColumn(['po_quantity_damaged', 'po_quantity_lost', 'support_ticket_id']);
        });
    }
};
