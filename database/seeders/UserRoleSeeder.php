<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['admin', 'sales'];
        foreach ($roles as $role) {
            Role::create(['name' => $role]);
        }

        $admin = User::first();
        $admin->assignRole('admin');

        $sales = User::skip(1)->take(PHP_INT_MAX)->get();
        $sales->each->assignRole('sales');
    }
}
