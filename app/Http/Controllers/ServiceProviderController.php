<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\ServiceProvider;

use Illuminate\Support\Facades\Log;
use Validator;
use Carbon;
use Helper;

class ServiceProviderController extends Controller
{
    //
    public function index(Request $request)
    {
        return Inertia::render('service-provider/index');
    }

    public function list(Request $request)
    {
        // echo $request->page_size;exit;
        if ($request->page_size==-1)
        {
            $data = ServiceProvider::when($request->sort_by, function ($query, $value) {
                $query->orderBy($value, request('order_by', 'asc'));
            })
            ->when(!isset($request->sort_by), function ($query) {
                $query->latest();
            })
            ->when($request->search, function ($query, $value) {
                $query->where('company_name', 'LIKE', '%'.$value.'%')
                ->orWhere("email",'LIKE','%'.$value.'%')
                ->orWhere("reference_number",'LIKE',$value.'%');
            })
            ->when(Helper::auth_role_dsp(),function($query){
                $query->whereIn("id",Helper::auth_dsp_list("service_provider_id"));
            })
            ->get();
        }
        else
        {
            $data = ServiceProvider::when($request->sort_by, function ($query, $value) {
                $query->orderBy($value, request('order_by', 'asc'));
            })
            ->when(!isset($request->sort_by), function ($query) {
                $query->latest();
            })
            ->when($request->search, function ($query, $value) {
                $query->where('company_name', 'LIKE', '%'.$value.'%')
                ->orWhere("email",'LIKE','%'.$value.'%')
                ->orWhere("reference_number",'LIKE',$value.'%');
            })
            ->when(Helper::auth_role_dsp(),function($query){
                $query->whereIn("id",Helper::auth_dsp_list("service_provider_id"));
            })
            ->paginate($request->page_size ?? 10);
        }
       

        return response()->json(["status"=>"success","data"=>$data], 200);
    }

    public function store(Request $request)
    {
        $data = $this->validate($request, [
            'email' => 'required|string|email',
            'company_name' => 'required|string',
            'category' => 'required|string',
            'company_code'=>'required|string',
            'status'=>'required|string',
            "fees"=>"required|array",
        ]);

        $data["reference_number"] = Helper::ref_number("DSP",20,"-");
        $data["code"] = gen_dsp_code();
        $data["secret"] = gen_random_string(24);
        $data["token"] = gen_random_string(16);

        $item = ServiceProvider::create($data);

        $fees = collect($data["fees"])->map(function($cell){
            return [
                "amount"=>$cell["amount"],
                "min"=>($cell["min"]) ? $cell["min"] : 0,
                "max"=>($cell["max"]) ? $cell["max"] : 0,
                "amount_type"=>$cell["amount_type"],
                "type"=>$cell["type"],
                "status"=>$cell["status"]
            ]; 
        });

        $item->fees()->createMany($fees);

        if ($request->image) FileController::saveImage($item->reference_number,"service_provider",$request->image);
        // EmailNotificationController::SuccessRegistration($item);

        return response()->json(['success' => 1,"message"=>"Success!"]);
    }

    public function update($id,Request $request)
    {
        $data = $this->validate($request, [
            'email' => 'required|string|email',
            'company_name' => 'required|string',
            'category' => 'required|string',
            'company_code'=>'required|string',
            "fees"=>"required|array",
            'status'=>'required|string',
        ]);

        $item = ServiceProvider::where("reference_number",$request->id)->first();

        $fees = collect($data["fees"])->map(function($cell){
            return [
                "amount"=>$cell["amount"],
                "min"=>($cell["min"]) ? $cell["min"] : 0,
                "max"=>($cell["max"]) ? $cell["max"] : 0,
                "amount_type"=>$cell["amount_type"],
                "type"=>$cell["type"],
                "status"=>$cell["status"]
            ]; 
        });
        
        $item->update([
            "email"=>$data["email"],
            "company_name"=>$data["company_name"],
            "category"=>$data["category"],
            "status"=>$data["status"],
            "company_code"=>$data["company_code"],
        ]);

        $item->fees()->delete();
        $item->fees()->createMany($fees);
        
        if ($request->image) FileController::saveImage($item->reference_number,"service_provider",$request->image);

        return response()->json(['success' => 1,"message"=>"Success!"]);
    }

    public function get($id)
    {
        $data = ServiceProvider::with("fees")->where("reference_number",$id)->first();
        return response()->json(["status"=>"success","data"=>$data], 200);
    }

    public function destroy(Request $request)
    {
        $item = ServiceProvider::where("reference_number",$request->id)->delete();
        return response()->json(['success' => 1,"message"=>"Success!"]);
    }

    public function validateRequest(Request $request)
    {
        $fees = ($request->step_no==1) ? 'required|' : '';

        $validator = Validator::make($request->all(), [
            'step_no'=>'required',
            'email' => 'required|string|email',
            'company_name' => 'required|string',
            'category' => 'required|string',
            'company_code'=>'required|string',
            "fees"=>$fees."array",
            'status'=>'required|string'
        ]);

        if($validator->fails()) 
        {
            $log = ['success' => 0, 'errors' => $validator->getMessageBag()->toArray()];
            return response()->json($log, 400);
        }

        return response()->json(['success' => 1,"message"=>"Success!"]);
    }
}
