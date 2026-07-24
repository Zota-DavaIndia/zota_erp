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
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_id')->unsigned();
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');

            $table->string('ticket_number')->unique();

            // The store location that raised the ticket.
            $table->integer('location_id')->unsigned();
            $table->foreign('location_id')->references('id')->on('business_locations')->onDelete('cascade');

            // The GRN line the damage/loss was reported on (the Damage/Loss Report row).
            $table->integer('purchase_line_id')->unsigned();
            $table->foreign('purchase_line_id')->references('id')->on('purchase_lines')->onDelete('cascade');
            $table->integer('transaction_id')->unsigned();
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');

            // The originating PO line/transaction, resolved at creation time (nullable: a purchase
            // need not always be against a PO).
            $table->integer('purchase_order_line_id')->unsigned()->nullable();
            $table->foreign('purchase_order_line_id')->references('id')->on('purchase_lines')->onDelete('cascade');
            $table->integer('purchase_order_id')->unsigned()->nullable();
            $table->foreign('purchase_order_id')->references('id')->on('transactions')->onDelete('cascade');

            $table->enum('ticket_type', ['loss_short', 'in_transit_damage', 'mixed']);

            // Snapshot of the reported quantities/reason at the time the ticket was raised,
            // independent of whatever the source purchase_line is edited to afterwards.
            $table->decimal('quantity_damaged', 22, 4)->default(0);
            $table->decimal('quantity_lost', 22, 4)->default(0);
            $table->string('damage_loss_reason')->nullable();
            $table->text('damage_loss_note')->nullable();

            $table->enum('status', ['open', 'closed'])->default('open');

            $table->integer('closure_reason_id')->unsigned()->nullable();
            $table->foreign('closure_reason_id')->references('id')->on('support_ticket_closure_reasons')->onDelete('restrict');
            $table->text('closure_note')->nullable();

            $table->integer('raised_by')->unsigned();
            $table->foreign('raised_by')->references('id')->on('users')->onDelete('cascade');
            $table->integer('closed_by')->unsigned()->nullable();
            $table->foreign('closed_by')->references('id')->on('users')->onDelete('cascade');
            $table->dateTime('closed_at')->nullable();

            $table->timestamps();

            $table->index('business_id');
            $table->index('location_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('support_tickets');
    }
};
