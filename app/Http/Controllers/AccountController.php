<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\ServiceProvider;
use App\Models\Account;
use App\Models\User;


use Illuminate\Support\Facades\Log;
use Validator;
use Auth;
use Helper;

class AccountController extends Controller
{
    //
    public function index(Request $request)
    {
        return Inertia::render('account/index');
    }

    public function list(Request $request)
    {
        $data = Account::with(['user'])->when($request->sort_by, function ($query, $value) {
            $query->orderBy($value, request('order_by', 'asc'));
        })
        ->when(!isset($request->sort_by), function ($query) {
            $query->latest();
        })
        ->when($request->search, function ($query, $value) {
            $query->whereRaw("concat(first_name,' ',last_name) LIKE ?",['%'.$value.'%'])
            ->orWhere("last_name",'LIKE','%'.$value.'%');
        })
        ->paginate($request->page_size ?? 10);

        return response()->json(["status"=>1,"data"=>$data], 200);
    }

    public function get($id)
    {
        $data = Account::with(["user",'serviceProvider.data','region'])->where("account_number",$id)->first();
        return response()->json(["status"=>"success","data"=>$data], 200);
    }

    public function store(Request $request)
    {
        
        $service_provider = $request->role=="DSP" ? "required|array|min:1" : "";
        $region = $request->role=="RDO" ? "required|array|min:1" : "";
        $data = $this->validate($request, [
            'email' => 'required|email|unique:users,email',
            'username' => 'required|alpha_dash|unique:users,username',
            'password' => 'required|alpha_dash|same:confirm_password',
            'service_provider' => $service_provider,
            'region' => $region
        ]);

        $user = User::create([
            "username"=>$request->username,
            "email"=>$request->email,
            "password"=>bcrypt($request->password)
        ]);

        $account_number = $ref = Helper::ref_number("A",20);
        $account = Account::create([
            "user_id"=>$user->id,
            "account_number"=>$account_number,
            "first_name"=>$request->first_name,
            "middle_name"=>$request->middle_name,
            "last_name"=>$request->last_name
        ]);

        if($request->role=="DSP")
        {
            $service_provider = collect($data["service_provider"])->map(function ($dsp) {
                return ["service_provider_id"=>$dsp['service_provider_id']];
            });
            $account->serviceProvider()->createMany($service_provider);
        }
        
        $user->attachRole($request->role);

        if ($request->image)
            FileController::saveImage($account->account_number,"accounts",$request->image);

        return response()->json(['success' => 1,"message"=>"Success!"]);
    }

    public function validateRequest(Request $request)
    {
        $password = $request->password ? 'alpha_dash|' : '';
        $service_provider = "";
        if (!$request->id){
            $password = "required|";
        }

        if ($request->step_no==2){
            $service_provider = $request->role=="DSP" ? "required|array|min:1" : "";
        }

        $region = $request->role=="RDO" ? "required|array|min:1" : "";

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email,' . $request->uid,
            'username' => 'required|alpha_dash|unique:users,username,' . $request->uid,
            'step_no'=>'required',
            'password' => $password.'same:confirm_password',
            'service_provider' => $service_provider,
            'region' => $region
        ]);

        if($validator->fails()) 
        {
            $log = ['success' => 0, 'errors' => $validator->getMessageBag()->toArray()];
            return response()->json($log, 400);
        }

        return response()->json(['success' => 1,"message"=>"Success!"]);
    }

    public function update($id,Request $request)
    {
        $password = $request->password ? 'alpha_dash|' : '';
        $service_provider = $request->role=="DSP" ? "required|array|min:1" : "";
        $region = $request->role=="RDO" ? "required|array|min:1" : "";
        $data = $this->validate($request, [
            'email' => 'required|email|unique:users,email,' . $request->uid,
            'username' => 'required|alpha_dash|unique:users,username,' . $request->uid,
            'password' => $password.'same:confirm_password',
            'service_provider' => $service_provider,
            "region"=>$region
        ]);

        $item = Account::with(["user","serviceProvider"])->where("account_number",$id)->first();
        $item->update([
            "first_name"=>$request->first_name,
            "middle_name"=>$request->middle_name,
            "last_name"=>$request->last_name
        ]);
        
        if($request->role=="DSP")
        {
            $service_provider = collect($data["service_provider"])->map(function ($dsp) {
                return ["service_provider_id"=>$dsp['service_provider_id']];
            });
            $item->serviceProvider()->delete();
            $item->serviceProvider()->createMany($service_provider);
        }

        if($request->role=="RDO")
        {
            $region = collect($data["region"])->map(function ($region_code) {
                return ["region_code"=>$region_code];
            });

            Log::info($region);
            $item->region()->delete();
            $item->region()->createMany($region);
        }

        if ($request->password)
        {
            $item->user->update([
                "email"=>$request->email,
                "username"=>$request->username,
                "password"=>bcrypt($request->password),
            ]);
        }
        else
        {
            $item->user->update([
                "email"=>$request->email,
                "username"=>$request->username,
            ]);
        }
        $item->user->detachRole($item->user->role_name); 
        $item->user->attachRole($request->role);
        
        if ($request->image)
            FileController::saveImage($item->account_number,"accounts",$request->image);

        return response()->json(['success' => 1,"message"=>"Success!"]);
    }

    public function destroy(Request $request)
    {
        $item = Account::where("account_number",$request->id)->delete();
        return response()->json(['success' => 1,"message"=>"Success!"]);
    }

    public function profile()
    {
        return Inertia::render('account/profile');
    }

    public function updateProfile(Request $request){
        $password = $request->password ? 'alpha_dash|' : '';
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email,' . $user->id,
            'username' => 'required|alpha_dash|unique:users,username,' . $user->id,
            'password' => $password.'same:confirm_password',
            'confirm_password' => $password.'same:password',
        ]);

        if($validator->fails()) 
        {
            $log = ['success' => 0, 'errors' => $validator->getMessageBag()->toArray()];
            return response()->json($log, 400);
        }

        $user->email = $request->email;
        $user->username = $request->username;
        if ($request->password) $user->password = bcrypt($request->password);
        $user->save();
        $user->account()->update([
            'first_name'=>$request->first_name,
            'middle_name'=>$request->middle_name,
            'last_name'=>$request->last_name,
        ]);

        if ($request->image)
            FileController::saveImage($user->account->account_number,"accounts",$request->image);

        return response()->json(['success' => 1,'data',"message"=>"Success!"]);
    }
}
