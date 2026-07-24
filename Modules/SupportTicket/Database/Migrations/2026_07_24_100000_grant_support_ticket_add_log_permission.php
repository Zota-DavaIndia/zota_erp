<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Adding a progress log is a distinct capability from closing a ticket -
     * a role can be given one without the other. Admin roles get it by
     * default, same as the other management-level ticket permissions.
     *
     * @return void
     */
    public function up()
    {
        Permission::firstOrCreate(['name' => 'support_ticket.add_log', 'guard_name' => 'web']);

        $permission = Permission::where('name', 'support_ticket.add_log')->first();
        foreach (Role::where('name', 'like', 'Admin#%')->get() as $role) {
            $role->givePermissionTo($permission);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Permission::where('name', 'support_ticket.add_log')->delete();
    }
};
