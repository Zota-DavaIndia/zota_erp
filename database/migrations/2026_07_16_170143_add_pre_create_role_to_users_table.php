<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add a `pre_create_role` column to `users`.
     *
     * When the super admin pre-creates a user, they can pick a role
     * (the same role names that already exist on the current business,
     * e.g. "Cashier", "Admin", "Salesman"). The role NAME is stored
     * here without the `#<business_id>` suffix.
     *
     * When the pre-created user is later attached to a new business
     * during business creation, the system looks up the role with
     * the same name in the new business (e.g. "Cashier#5") and
     * assigns that role to the user. The role's permissions carry
     * over because Spatie permissions are stored on the role, and
     * every business has its own copy of the default roles with
     * the same name and same permissions.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'pre_create_role')) {
                $table->string('pre_create_role', 100)
                    ->nullable()
                    ->after('allow_login')
                    ->comment('Role name to be granted to a pre-created user when they are assigned to a new business.');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'pre_create_role')) {
                $table->dropColumn('pre_create_role');
            }
        });
    }
};
