<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
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
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'admin']);
        Role::firstOrCreate(['name' => 'member', 'guard_name' => 'admin']);

        // Client-side role lives on the "client" guard.
        Role::firstOrCreate(['name' => 'client', 'guard_name' => 'client']);

        // Permissions du panneau de configuration (doc 02 — §6.bis.4),
        // sur le guard "admin". L'admin les reçoit toutes par défaut.
        $settingPermissions = [
            'manage-settings',      // accès général au panneau
            'view-settings',
            'manage-pricing',       // peut changer prix / taux
            'manage-feature-flags', // peut activer/désactiver des modules
            'manage-gateways',      // peut changer les passerelles de paiement
        ];

        foreach ($settingPermissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'admin']);
        }

        $admin->givePermissionTo($settingPermissions);
    }
}
