<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Whitelist;

class WhitelistTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $data = [
            [
                "name"=>"LOCALHOST2",
                "ip_address"=>"127.0.0.1",
                "status"=>"ACTIVE",
            ],
        ];

        Whitelist::insert($data);
    }
}
