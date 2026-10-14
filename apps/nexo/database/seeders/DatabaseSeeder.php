<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $db = DB::class;
        if ($db::table('organizations')->exists()) {
            return;
        }
        foreach (['A', 'B'] as $org) {
            $db::table('organizations')->insert(['id' => 'ORG-'.$org, 'name' => 'Organización '.$org]);
            foreach (['Solicitante', 'Coordinador', 'Bodega', 'Auditor'] as $role) {
                $db::table('users')->insert(['name' => 'Demo '.$role, 'email' => 'org-'.strtolower($org).'-'.strtolower($role).'@example.test',
                    'password' => Hash::make('DemoNexo360!'), 'organization_id' => 'ORG-'.$org, 'role' => $role, 'created_at' => now(), 'updated_at' => now()]);
            }
            foreach (['KIT-ALIMENTO' => ($org === 'A' ? 10 : 4), 'KIT-HIGIENE' => ($org === 'A' ? 8 : 5), 'KIT-AGUA' => ($org === 'A' ? 20 : 6)] as $sku => $quantity) {
                $db::table('inventory_items')->insert(['organization_id' => 'ORG-'.$org, 'sku' => $sku, 'fisico' => $quantity, 'reservado' => 0, 'active' => true]);
            }
        }
    }
}
