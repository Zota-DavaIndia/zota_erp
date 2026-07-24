<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::table('support_tickets', function (Blueprint $table) {
            $table->dateTime('tat_due_at')->nullable()->after('quantity_lost');
            $table->index('tat_due_at');
        });

        // Widen the status enum to add 'delayed' - a ticket past its TAT that
        // hasn't been closed yet. Raw SQL since Blueprint::enum()->change()
        // doesn't reliably alter MySQL enums via doctrine/dbal.
        DB::statement("ALTER TABLE support_tickets MODIFY status ENUM('open', 'delayed', 'closed') NOT NULL DEFAULT 'open'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('support_tickets')->where('status', 'delayed')->update(['status' => 'open']);
        DB::statement("ALTER TABLE support_tickets MODIFY status ENUM('open', 'closed') NOT NULL DEFAULT 'open'");

        Schema::table('support_tickets', function (Blueprint $table) {
            $table->dropColumn('tat_due_at');
        });
    }
};
