<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Every account gets support_ticket.create/view_own by default; only
     * Admin roles get the consolidated dashboard + manage/close by default.
     * New businesses created afterwards inherit this automatically, since
     * their roles are cloned from the template business's role permissions.
     *
     * @return void
     */
    public function up()
    {
        foreach (['support_ticket.create', 'support_ticket.view_own', 'support_ticket.view_all', 'support_ticket.manage'] as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $default_permissions = Permission::whereIn('name', ['support_ticket.create', 'support_ticket.view_own'])->get();
        foreach (Role::all() as $role) {
            $role->givePermissionTo($default_permissions);
        }

        $admin_permissions = Permission::whereIn('name', ['support_ticket.view_all', 'support_ticket.manage'])->get();
        foreach (Role::where('name', 'like', 'Admin#%')->get() as $role) {
            $role->givePermissionTo($admin_permissions);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Permission::whereIn('name', ['support_ticket.create', 'support_ticket.view_own', 'support_ticket.view_all', 'support_ticket.manage'])->delete();
    }
};
