<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        // Employee::factory(100)->create();

        $this->call(RolesTableSeeder::class);
        $this->call(AccountsTableSeeder::class);
        $this->call(ServiceProvidersTableSeeder::class);
        $this->call(WhitelistTableSeeder::class);
        $this->call(RegionsTableSeeder::class);
        $this->call(SeriesCollectionSeeder::class);
        
    }
}
