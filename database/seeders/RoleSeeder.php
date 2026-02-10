<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['admin', 'operations_manager', 'dispatcher'];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Assign Spatie roles to existing users based on their role column
        $users = \App\Models\User::all();
        foreach ($users as $user) {
            if ($user->role && !$user->hasAnyRole($roles)) {
                $user->assignRole($user->role->value);
            }
        }
    }
}
