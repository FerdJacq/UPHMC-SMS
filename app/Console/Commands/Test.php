<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use DB;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $user = DB::table("users")->first();
        echo json_encode($user);
    }
}
