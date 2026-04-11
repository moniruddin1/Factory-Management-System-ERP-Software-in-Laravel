<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ডিফাইন করা পারমিশন লিস্ট
        $permissions = [
            // Master Setup Permissions
            'view-categories', 'manage-categories',
            'view-units', 'manage-units',
            'view-variations', 'manage-variations',

            // User & Role Permissions
            'view-roles', 'manage-roles',
            'view-users', 'manage-users',

            // Inventory Permissions (Upcoming)
            'view-inventory', 'manage-inventory',
            'view-raw-materials', 'manage-raw-materials',

                //Delete Actions
                'delete-categories',
                'delete-units',
                'delete-variations',
                'delete-users',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        // একটি সুপার অ্যাডমিন রোল তৈরি করা এবং সব পারমিশন দিয়ে দেওয়া
        $adminRole = Role::findOrCreate('Super Admin');
        $adminRole->givePermissionTo(Permission::all());
    }
}
