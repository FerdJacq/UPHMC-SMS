<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Account;
use App\Models\User;
use App\Models\AccountRegion;

use Helper;

class AccountsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $user = User::create([
            "username"=>"admin",
            "email"=>"jondeerigor@gmail.com",
            "password"=>bcrypt("admin")
        ]);

        $account_number = $ref = Helper::ref_number("A",20);
        $account = Account::create([
            "user_id"=>$user->id,
            "account_number"=>$account_number,
            "first_name"=>"jondee",
            "middle_name"=>"",
            "last_name"=>"rigor"
        ]);
        $user->addRole("admin");
        //==========================================


        $user = User::create([
            "username"=>"dsp",
            "email"=>"dsp@gmail.com",
            "password"=>bcrypt("dsp")
        ]);

        $account_number = $ref = Helper::ref_number("A",20);
        $account = Account::create([
            "user_id"=>$user->id,
            "account_number"=>$account_number,
            "first_name"=>"jondee",
            "middle_name"=>"",
            "last_name"=>"rigor"
        ]);

        $account->serviceProvider()->create(["service_provider_id"=>1]);
        $user->addRole("dsp");
        //==========================================

        $user = User::create([
            "username"=>"seller",
            "email"=>"seller@gmail.com",
            "password"=>bcrypt("seller")
        ]);

        $account_number = $ref = Helper::ref_number("A",20);
        $account = Account::create([
            "user_id"=>$user->id,
            "account_number"=>$account_number,
            "first_name"=>"H&L GOODS",
            "middle_name"=>"",
            "last_name"=>""
        ]);

        $account->serviceProvider()->createMany([
            ["service_provider_id"=>1],
            ["service_provider_id"=>2],
            ["service_provider_id"=>3],
            ["service_provider_id"=>4],
            ["service_provider_id"=>5],
        ]);

        $user->addRole("seller");
        //==========================================

        $user = User::create([
            "username"=>"bir",
            "email"=>"bir@gmail.com",
            "password"=>bcrypt("bir")
        ]);

        $account_number = $ref = Helper::ref_number("A",20);
        $account = Account::create([
            "user_id"=>$user->id,
            "account_number"=>$account_number,
            "first_name"=>"jondee",
            "middle_name"=>"",
            "last_name"=>"rigor"
        ]);

        $user->addRole("bir");

         //==========================================

        $region_codes = ["","01","02","03","04","05","06","07","08","09","10","11","12","13","14"];
         foreach ($region_codes as $region_code) {

            $username = "rdo".$region_code;
            $user = User::create([
                "username"=>$username,
                "email"=>$username."@gmail.com",
                "password"=>bcrypt($username)
            ]);
    
            $account_number = $ref = Helper::ref_number("A",20);
            $account = Account::create([
                "user_id"=>$user->id,
                "account_number"=>$account_number,
                "first_name"=>"Regional District Office",
                "middle_name"=>"",
                "last_name"=>$region_code
            ]);
    
            $user->addRole("rdo");
    
            $account->region()->create(["region_code"=>$region_code]);
         }

        //==========================================

        
    }
}
