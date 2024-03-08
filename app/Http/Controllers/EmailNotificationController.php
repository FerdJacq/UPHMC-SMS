<?php

namespace App\Http\Controllers;

use Notification;
use App\Models\ServiceProvider;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Mail\SuccessRegistration;
use App\Mail\Receipt;
use App\Mail\OTP;

use Illuminate\Support\Facades\Log;

use Mail;

class EmailNotificationController extends Controller
{
    //
    

    public static function SuccessRegistration($item)
    {
        try {
            Mail::to($item->email)->send(new SuccessRegistration($item));
            return true;
        } catch (\Exception $e) {
           Log::error($e);
           return false;
        }
    }

    public static function OTP($item)
    {
        try {
            Mail::to($item->email)->send(new OTP($item));
            return true;
        } catch (\Exception $e) {
           Log::error($e);
           return false;
        }
    }

    public static function officialReceipt($reference_number)
    {
        try {
            $item = Transaction::with(['customer','details','ServiceProvider'])
            ->where("reference_number",$reference_number)
            ->first();
            Mail::to($item->customer->email)->send(new Receipt($item));
            return true;
        } catch (\Exception $e) {
           Log::error($e);
           return false;
        }
    }

    public function test_mail()
    {
        $item = Transaction::with(['customer','details','ServiceProvider'])->first();
        try {
            Mail::to("jondeerigor@gmail.com")->send(new Receipt($item));
            return true;
        } catch (\Exception $e) {
           Log::info($e);
        }
    }
}
