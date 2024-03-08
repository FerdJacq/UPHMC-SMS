<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SeriesCollection;

class SeriesCollectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                "account_id"=>"1",
                "service_provider_id"=>"1",
                "company_code"=>"SP",
                "prefix"=>"PX",
                "suffix"=>"SX",
                "starting_no"=>"1",
                "ending_no"=>"20000",
                "total"=>"20000",
                "length"=>"8",
            ],
            [
                "account_id"=>"1",
                "service_provider_id"=>"2",
                "company_code"=>"G",
                "prefix"=>"PX",
                "suffix"=>"SX",
                "starting_no"=>"1",
                "ending_no"=>"20000",
                "total"=>"20000",
                "length"=>"8",
            ],
            [
                "account_id"=>"1",
                "service_provider_id"=>"3",
                "company_code"=>"SM",
                "prefix"=>"PX",
                "suffix"=>"SX",
                "starting_no"=>"1",
                "ending_no"=>"20000",
                "total"=>"20000",
                "length"=>"8",
            ],
            [
                "account_id"=>"1",
                "service_provider_id"=>"4",
                "company_code"=>"TK",
                "prefix"=>"PX",
                "suffix"=>"SX",
                "starting_no"=>"1",
                "ending_no"=>"20000",
                "total"=>"20000",
                "length"=>"8",
            ],
            [
                "account_id"=>"1",
                "service_provider_id"=>"5",
                "company_code"=>"BPH",
                "prefix"=>"PX",
                "suffix"=>"SX",
                "starting_no"=>"1",
                "ending_no"=>"20000",
                "total"=>"20000",
                "length"=>"8",
            ],
        ];

        SeriesCollection::insert($data);
    }
}
