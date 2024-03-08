<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;


class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        $admin = Role::create([
            'name' => 'ADMIN',
            'display_name' => 'Administrator', // optional
            'description' => '', // optional
        ]);
            
        $bir = Role::create([
            'name' => 'BIR',
            'display_name' => 'BIR', // optional
            'description' => '', // optional
        ]);

        $rdo = Role::create([
            'name' => 'RDO',
            'display_name' => 'Revenue District Office', // optional
            'description' => '', // optional
        ]);

        $dsp = Role::create([
            'name' => 'DSP',
            'display_name' => 'Digital Service Provider', // optional
            'description' => '', // optional
        ]);

        $dsp = Role::create([
            'name' => 'SELLER',
            'display_name' => 'Online Sellers', // optional
            'description' => '', // optional
        ]);

        $module_accounts = Permission::create([
            'name' => 'module-accounts',
            'display_name' => 'Module Account', // optional
            'description' => 'Allow module Account', // optional
        ]);

        $module_service_providers = Permission::create([
            'name' => 'module-service-providers',
            'display_name' => 'Module Service Providers', // optional
            'description' => 'Allow module Service Providers', // optional
        ]);

        $admin->syncPermissions([$module_accounts, $module_service_providers]);

    }
}
