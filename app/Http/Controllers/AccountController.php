<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Account;
use App\Models\User;


use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Library\Helper;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class AccountController extends Controller
{
    //
    public function index(Request $request)
    {
        return Inertia::render('account/index');
    }

    public function list(Request $request)
    {
        $search = $request->search;
        $column = $request->column;
        $order = $request->order;
        $start_date = isset($request->start_date) ? Carbon::parse($request->start_date)->copy()->startOfDay()->toDateTimeString() : null;
        $end_date = isset($request->end_date) ? Carbon::parse($request->end_date)->copy()->endOfDay()->toDateTimeString() : null;


        $data = Account::with(['user'])
            ->when($search, function ($query, $value) 
            {
                $query->whereRaw("concat(first_name,' ',last_name) LIKE ?",['%'.$value.'%'])
                    ->orWhere("last_name",'LIKE','%'.$value.'%');
            })
            ->when($start_date && $end_date, function ($query) use ($start_date, $end_date)
                {
                    $query->whereBetween('created_at',[$start_date, $end_date]);
                })
            ->when($order && $column, function ($query) use ($column, $order) 
                {
                    $query->orderBy($column, $order);
                }, 
                    function ($query) 
                    {
                        $query->orderBy("id", "desc");
                    }
                )
            ->paginate($request->limit ?? 10);

        return response()->json(["status" => 1,"data" => $data], 200);
    }

    public function get($id)
    {
        $data = Account::with(["user"])
            ->where("account_number",$id)
            ->first();

        return response()->json(["status"=>"success", "data"=>$data], 200);
    }

    public function validateRequest(Request $request)
    {
        $rules = [];
        $password = ["nullable|"];
        $user_id = $request->user_id;
    
        if (!$user_id)
        {
            $password = [ "required", "alpha_dash", "same:confirm_password"];
        }

        switch ($request->step_no) {
            case 0:
                $rules = [
                    'role_name' => ['required', 'string'],
                    'email' => ['required', 'email', "unique:users,email,$user_id"],
                    'username' => ['required', 'string', 'alpha_dash', "unique:users,username,$user_id"],
                    'password' => $password,
                ];
                break;
        
            case 1:
                $rules = [
                    'first_name' => ['required', 'string'],
                    'last_name' => ['required', 'string'],
                    'middle_name' => ['nullable', 'string'],
                ];
                break;
        
            default:
                $rules = [];
                break;
        }

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()) 
        {
            $log = ['success' => 0, 'errors' => $validator->getMessageBag()->toArray()];
            return response()->json($log, 400);
        }

        return response()->json(['success' => 1,"message"=>"Success!"]);
    }
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role_name' => ['required', 'string'],
            'email' => ['required', 'email', "unique:users,email"],
            'username' => ['required', 'string', 'alpha_dash', "unique:users,username"],
            'password' => [ "required", "alpha_dash", "same:confirm_password"],
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'middle_name' => ['nullable', 'string'],
        ]);

        if($validator->fails()) 
        {
            $log = ['success' => 0, 'errors' => $validator->getMessageBag()->toArray()];
            return response()->json($log, 400);
        }

        try 
        {
            return DB::transaction(function () use ($request)
            {
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
                
                $user->addRole($request->role_name);
        
                if ($request->new_avatar) FileController::saveImage($account->account_number,"accounts",$request->new_avatar);
        
                return response()->json(['success' => 1,"message"=>"Success!"]);
            });
        } 
        catch(\Exception $e) 
        {
            return response()->json(['status' => 0, 'message' => $e->getMessage()], 400);
        }
    }
    public function update($id,Request $request)
    {
        $user_id = $request->user_id;
        $validator = Validator::make($request->all(), [
            'id' => ['required', "exists:accounts,id"],
            'role_name' => ['required', 'string'],
            'email' => ['required', 'email', "unique:users,email,$user_id"],
            'username' => ['required', 'string', 'alpha_dash', "unique:users,username,$user_id"],
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'middle_name' => ['nullable', 'string'],
        ]);

        if($validator->fails()) 
        {
            $log = ['success' => 0, 'errors' => $validator->getMessageBag()->toArray()];
            return response()->json($log, 400);
        }

        $account = Account::where("id",$id)
            ->first();

        $account->update([
            "first_name" => $request->first_name,
            "middle_name" => $request->middle_name,
            "last_name" => $request->last_name
        ]);

        $user = User::where("id",$account->user_id)
            ->first();

        if($user)
        {
            $user->update([
                "email"=>$request->email,
                "username"=>$request->username,
            ]);
        
            $user->removeRole($user->role_name); 
            $user->addRole($request->role_name);
        }
        
        if ($request->new_avatar) FileController::saveImage($account->account_number,"accounts",$request->new_avatar);

        return response()->json(['success' => 1,"message"=>"Success!"]);
    }
    public function destroy($account_number, Request $request)
    {
        $item = Account::where("account_number",$account_number)
            ->delete();

        return response()->json(['success' => 1,"message"=>"Success!"]);
    }
}
