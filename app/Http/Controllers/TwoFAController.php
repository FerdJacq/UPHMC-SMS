<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Jenssegers\Agent\Agent;

use Inertia\Inertia;
use App\Models\UserCodes;
use App\Models\UserDevices;
use Session;

use Carbon\Carbon;

class TwoFAController extends Controller
{
    //

    public function index()
    {
        // return "here";
        if (Session::get('user_2fa'))  return redirect('/');
        
        $this->generateCode();

        return Inertia::render('auth/otp',[]);
    }

    public function validateOTP(Request $request)
    {
        $request->validate([
            'otp'=>'required',
        ]);
        
        $ip = request()->ip();
        $platform = Session::get('platform');
        $browser = Session::get('browser');
        $device_name = Session::get('device_name');
        $device_id = Session::get('device_id');
  
        $user_id = auth()->user()->id;
        $data = UserCodes::where('user_id', $user_id)
                ->where('code', $request->otp)
                ->first();
  
        if (!is_null($data)) {

            $data->status="USED";
            $data->save();

            $device = new UserDevices();
            $device->user_id = $user_id;
            $device->device_id = $device_id;
            $device->device_name = $device_name;
            $device->os = $platform;
            $device->browser = $browser;
            $device->ip = $ip;
            $device->save();
            Session::put('user_2fa', $user_id);
            
            return response()->json(["status"=>1,"message"=>"Success!"], 200);
        }
        
        return response()->json(["status"=>0,"message"=>"You entered wrong code"], 500);
    }

    public function resend()
    {
        $user_id = auth()->user()->id;
        $data = UserCodes::where('user_id', $user_id)
            ->where('expires_at', '>=', now())
            ->where("status","AVAILABLE")
            ->first();
        
        if (!$data){
            $data = new UserCodes();
            $data->user_id = $user_id;
            $data->code = strtoupper(gen_random_string(6));
            $data->expires_at= Carbon::now()->addMinute(30);
            $data->save();
        }

        $data->ip = request()->ip();
        $data->platform = Session::get('platform');
        $data->browser = Session::get('browser');
        $data->device_name = Session::get('device_name');
        $data->email = auth()->user()->email;
        $notify = EmailNotificationController::OTP($data);
  
        return response()->json(["status"=>1,"message"=>"Security code has been sent to your email!"], 200);
    }

    private function generateCode()
    {

        $user_id = auth()->user()->id;
        $data = UserCodes::where('user_id', $user_id)
                ->where('expires_at', '>=', now())
                ->where("status","AVAILABLE")
                ->first();
        
        if (!$data){
            $data = new UserCodes();
            $data->user_id = $user_id;
            $data->code = strtoupper(gen_random_string(6));
            $data->expires_at= Carbon::now()->addMinute(30);
            $data->save();

            $data->ip = request()->ip();
            $data->platform = Session::get('platform');
            $data->browser = Session::get('browser');
            $data->device_name = Session::get('device_name');
            $data->email = auth()->user()->email;
            $notify = EmailNotificationController::OTP($data);
        }
    }
}
