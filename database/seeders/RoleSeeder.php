<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin users resolve to the "admin" guard, so their roles must be
        // created on that guard (the two the users form offers: admin, member).
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'admin']);
        Role::firstOrCreate(['name' => 'member', 'guard_name' => 'admin']);

        // Client-side role lives on the "client" guard.
        Role::firstOrCreate(['name' => 'client', 'guard_name' => 'client']);
    }
}
