<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // ১. রোল তৈরি
        $adminRole = Role::create(['name' => 'Admin']);
        Role::create(['name' => 'Manager']);
        Role::create(['name' => 'Store Keeper']);
        Role::create(['name' => 'Production Manager']);
        Role::create(['name' => 'Accountant']);

        // ২. সুপার অ্যাডমিন ইউজার তৈরি
        $admin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@factory.com',
            'password' => Hash::make('12345678'),
        ]);

        // ৩. রোল অ্যাসাইন
        $admin->assignRole($adminRole);
    }
}
