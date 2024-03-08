<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class RegionsTableSeeder extends Seeder
{
    public function run()
    {
        
        \DB::table('regions')->delete();
        
        \DB::table('regions')->insert(array (
            0 => 
            array (
                'id' => 1,
                'region_code' => '01',
                'name' => 'REGION I',
                'region_desc' => 'ILOCOS REGION',
            ),
            1 => 
            array (
                'id' => 2,
                'region_code' => '02',
                'name' => 'REGION II',
                'region_desc' => 'CAGAYAN VALLEY',                
            ),
            2 => 
            array (
                'id' => 3,
                'region_code' => '03',
                'name' => 'REGION III',
                'region_desc' => 'CENTRAL LUZON',                 
            ),
            3 => 
            array (
                'id' => 4,
                'region_code' => '04',
                'name' => 'REGION IV-A',
                'region_desc' => 'CALABARZON',
            ),
            4 => 
            array (
                'id' => 5,
                'region_code' => '05',
                'name' => 'REGION V',
                'region_desc' => 'BICOL REGION',
            ),
            5 => 
            array (
                'id' => 6,
                'region_code' => '06',
                'name' => 'REGION VI',
                'region_desc' => 'WESTERN VISAYAS',
            ),
            6 => 
            array (
                'id' => 7,
                'region_code' => '07',
                'name' => 'REGION VII',
                'region_desc' => 'CENTRAL VISAYAS',
            ),
            7 => 
            array (
                'id' => 8,
                'region_code' => '08',
                'name' => 'REGION VIII',
                'region_desc' => 'EASTERN VISAYAS',
            ),
            8 => 
            array (
                'id' => 9,
                'region_code' => '09',
                'name' => 'REGION IX',
                'region_desc' => 'ZAMBOANGA PENINSULA',
            ),
            9 => 
            array (
                'id' => 10,
                'region_code' => '10',
                'name' => 'REGION X',
                'region_desc' => 'NORTHERN MINDANAO',
            ),
            10 => 
            array (
                'id' => 11,
                'region_code' => '11',
                'name' => 'REGION XI',
                'region_desc' => 'DAVAO REGION',
            ),
            11 => 
            array (
                'id' => 12,
                'region_code' => '12',
                'name' => 'REGION XII',
                'region_desc' => 'SOCCSKSARGEN',
            ),
            12 => 
            array (
                'id' => 13,
                'region_code' => '13',
                'name' => 'NCR',
                'region_desc' => 'NATIONAL CAPITAL REGION',
            ),
            13 => 
            array (
                'id' => 14,
                'region_code' => '14',
                'name' => 'CAR',
                'region_desc' => 'CORDILLERA ADMINISTRATIVE REGION',
            ),
            14 => 
            array (
                'id' => 15,
                'region_code' => '15',
                'name' => 'ARMM',
                'region_desc' => 'AUTONOMOUS REGION IN MUSLIM MINDANAO',
            ),
            15 => 
            array (
                'id' => 16,
                'region_code' => '16',
                'name' => 'REGION XIII',
                'region_desc' => 'CARAGA',
            ),
            16 => 
            array (
                'id' => 17,
                'region_code' => '17',
                'name' => 'REGION IV-B',
                'region_desc' => 'MIMAROPA',
            ),
            17 => 
            array (
                'id' => 18,
                'region_code' => '18',
                'name' => 'NIR',
                'region_desc' => 'NEGROS ISLAND REGION',
            ),
        ));         
    }
}