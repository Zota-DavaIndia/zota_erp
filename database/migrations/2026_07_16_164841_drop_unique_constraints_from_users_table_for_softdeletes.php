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
     * The original users table was created with hard UNIQUE indexes
     * on `username` and `email`. Because the User model uses
     * SoftDeletes, this prevents the same username / email from
     * being reused after a previous row is soft-deleted, which
     * caused "Duplicate entry" errors when the super admin tries
     * to re-create a user that was once deleted.
     *
     * We drop the unique indexes and replace them with plain
     * non-unique indexes (for query performance). Uniqueness is
     * now enforced at the application layer via
     * Illuminate\Validation\Rule::unique()->whereNull('deleted_at'),
     * which correctly ignores soft-deleted rows.
     *
     * @return void
     */
    public function up()
    {
        // Drop the hard unique constraints.
        // Use hasIndex/Doctrine introspection when available, else
        // a guarded raw SQL fallback so this is idempotent.
        $this->dropUniqueIfExists('users', 'users_username_unique');
        $this->dropUniqueIfExists('users', 'users_email_unique');

        // Re-add as non-unique indexes for query performance.
        Schema::table('users', function (Blueprint $table) {
            $table->index('username');
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop the non-unique indexes we added.
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['username']);
            $table->dropIndex(['email']);
        });

        // Re-add the hard unique constraints.
        Schema::table('users', function (Blueprint $table) {
            $table->unique('username');
            $table->unique('email');
        });
    }

    /**
     * Drop a unique index by name on a table, no-op if it does not
     * exist (so the migration is safe to re-run on partial state).
     */
    private function dropUniqueIfExists(string $table, string $indexName): void
    {
        $database = DB::getDatabaseName();
        $exists = DB::selectOne(
            'SELECT COUNT(*) AS c FROM information_schema.statistics
              WHERE table_schema = ? AND table_name = ? AND index_name = ?',
            [$database, $table, $indexName]
        );

        if ($exists && (int) $exists->c > 0) {
            DB::statement("ALTER TABLE `{$table}` DROP INDEX `{$indexName}`");
        }
    }
};
