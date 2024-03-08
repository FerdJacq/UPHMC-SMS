<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Jenssegers\Agent\Agent;
use App\Models\UserDevices;

use Session;
use Log;


class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Inertia\Response
     */

    public function create()
    {
        return Inertia::render('auth/login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */

    public function store(LoginRequest $request)
    {
        $credentials = $request->only('username', 'password');
        $device_id =$request->cookie('aid');

        if (!$device_id) return response()->json(['success' => 0, "message" => "There was a problem with your browser, please reload the browser."],400);
        
        if (Auth::attempt($credentials)) {

            Session::put('device_id',$device_id);

            $agent = new Agent();
            $platform = $agent->platform();
            $browser = $agent->browser();
            $device_name = $browser . "/" . $platform;
            $ip = request()->ip();

            Session::put('platform',$platform);
            Session::put('browser',$browser);
            Session::put('device_name',$device_name);
            Session::put('ip',$ip);

            $saved_devices = UserDevices::where("device_id",$device_id)
            ->where("device_name",$device_name)
            ->where("user_id",Auth::user()->id)
            ->first();

            if ($saved_devices) Session::put('user_2fa', auth()->user()->id);
            if(!env('APP_TWO_FA', false)) Session::put('user_2fa', auth()->user()->id);

            return response()->json(['success' => 1, "data" => ""],200);
        }

        return response()->json(['success' => 0, "message" => "Invalid Credentials"],500);
    
        // $request->authenticate();

        // $request->session()->regenerate();

        // return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
