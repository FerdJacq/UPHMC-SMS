<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ServiceProvider;

class ServiceProvidersTableSeeder extends Seeder
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
                "reference_number"=>"DSP230512-912039212312",
                "code"=>"DSPNWLS",
                "token"=>"HGX5LRxgkzkBRixt",
                "secret"=>"cJ1RJnoPIsavMiko3N5hQ7ky",
                "email"=>"jondeerigor@gmail.com",
                "company_name"=>"SHOPEE",
                "category"=>"E-COMMERCE",
                "company_code"=>"SP",
                "address"=>"Seven/NEO, 5th Ave, Taguig, Metro Manila",
                "tin"=>"125-935-611-001",
                "registration_notified"=>"1"
            ],
            [
                "reference_number"=>"DSP230512-912039212313",
                "code"=>"DSPNWLT",
                "token"=>"HGX5LRxgkzkBRixt",
                "secret"=>"cJ1RJnoPIsavMiko3N5hQ7ky",
                "email"=>"jondeerigor@gmail.com",
                "company_name"=>"GRAB",
                "category"=>"SERVICE",
                "company_code"=>"G",
                "address"=>"Level 27F/28F Exquadra Tower, Lot 1A Exchange Road corner Jade Street, Ortigas Center, Pasig City, Philippines",
                "tin"=>"551-662-135-002",
                "registration_notified"=>"1"
            ],
            [
                "reference_number"=>"DSP230512-912039212314",
                "code"=>"DSPNWLU",
                "token"=>"HGX5LRxgkzkBRixt",
                "secret"=>"cJ1RJnoPIsavMiko3N5hQ7ky",
                "email"=>"jondeerigor@gmail.com",
                "company_name"=>"SHOPSM",
                "category"=>"E-COMMERCE",
                "company_code"=>"SM",
                "address"=>"10th floor, Mall of Asia Arena Annex Building, Coral way cor. J.W. Diokno Blvd., Mall of Asia Complex, Pasay City, Philippines",
                "tin"=>"993-999-190-003",
                "registration_notified"=>"1"
            ],
            [
                "reference_number"=>"DSP230512-912039212315",
                "code"=>"DSPNWLV",
                "token"=>"HGX5LRxgkzkBRixt",
                "secret"=>"cJ1RJnoPIsavMiko3N5hQ7ky",
                "email"=>"jondeerigor@gmail.com",
                "company_name"=>"TIKTOK",
                "category"=>"E-COMMERCE",
                "company_code"=>"TK",
                "address"=>"Culver City, 5800 Bristol Pkwy, United States",
                "tin"=>"325-535-215-004",
                "registration_notified"=>"1"
            ],
            [
                "reference_number"=>"DSP230512-912039212316",
                "code"=>"DSPNWLW",
                "token"=>"HGX5LRxgkzkBRixt",
                "secret"=>"cJ1RJnoPIsavMiko3N5hQ7ky",
                "email"=>"jondeerigor@gmail.com",
                "company_name"=>"BRITEPH",
                "category"=>"E-COMMERCE",
                "company_code"=>"BPH",
                "address"=>"828 Marcos Highway, Santolan Pasig City",
                "tin"=>"163-613-152-005",
                "registration_notified"=>"1"
            ]
            // [
            //     "code"=>gen_dsp_code(),
            //     "token"=>gen_random_string(16),
            //     "secret"=>gen_random_string(24),
            //     "email"=>"jondeerigor@gmail.com",
            //     "company_name"=>"LAZADA",
            //     "category"=>"E-COMMERCE",
            //     "company_code"=>"L",
            // ]
        ];

        ServiceProvider::insert($data);

        $service_provider = ServiceProvider::get();

        foreach ($service_provider as $item) {
           

            $fees = [
                ["amount"=>"2","min"=>"0","max"=>"0","amount_type"=>"PERCENTAGE","type"=>"TRANSACTION","status"=>"ACTIVE"],
                ["amount"=>"2","min"=>"500","max"=>"1000","amount_type"=>"PERCENTAGE","type"=>"TRANSACTION","status"=>"ACTIVE"],
                ["amount"=>"1","min"=>"0","max"=>"0","amount_type"=>"PERCENTAGE","type"=>"COMMISSION","status"=>"ACTIVE"],
                ["amount"=>"15","min"=>"0","max"=>"0","amount_type"=>"FIXED","type"=>"SERVICE","status"=>"ACTIVE"],
                ["amount"=>"3","min"=>"0","max"=>"500","amount_type"=>"PERCENTAGE","type"=>"SERVICE","status"=>"ACTIVE"],
            ];

            $item->fees()->createMany($fees);
        }
    }
}
