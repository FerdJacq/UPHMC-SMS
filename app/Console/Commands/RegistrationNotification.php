<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\ServiceProvider;

use Illuminate\Support\Facades\Log;
use App\Http\Controllers\EmailNotificationController;
use Illuminate\Support\Carbon;

class RegistrationNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'registration:notify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $data = ServiceProvider::where("registration_notified",0)->get();
        echo Carbon::now()->format('d M Y H:i');
        
        foreach ($data as $item) {
            $notify = EmailNotificationController::SuccessRegistration($item);
            if ($notify)
                $item->update(['registration_notified'=>1]);
        }
    }
}
