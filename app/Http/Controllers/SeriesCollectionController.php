<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

use App\Models\SeriesCollection;
use App\Models\Series;
use App\Models\SeriesUpload;
use App\Models\Account;
use App\Models\ServiceProvider;

use Auth;
use Log;
use Helper;
use Validator;


class SeriesCollectionController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('series_collection/index');
    }

    public function list(Request $request)
    {
        $start_date = $request->start_date ? date('Y-m-d', strtotime($request->start_date)) : "";
        $end_date =  $request->end_date ? date('Y-m-d', strtotime($request->end_date)) : "";

        $data = SeriesCollection::with(["ServiceProvider"])->when($request->sort_by, function ($query, $value) {
            $query->orderBy($value, request('order_by', 'asc'));
        })
        ->when(!isset($request->sort_by), function ($query) {
            $query->latest();
        })
        ->when($request->search, function ($query, $value) {
            $query->where('prefix', 'LIKE', '%'.$value.'%')
            ->orWhere("suffix",'LIKE','%'.$value.'%');
        })
        ->when($start_date && $end_date, function ($query, $value) use($start_date,$end_date) {
            $query->whereBetween(DB::raw('DATE(created_at)'), [$start_date, $end_date]);
        })
        ->when(Helper::auth_role_dsp(),function($query){
            $query->whereIn("service_provider_id",Helper::auth_dsp_list("service_provider_id"));
        })
        ->orderByDesc("id")
        ->paginate($request->page_size ?? 10);

        return response()->json(["status"=>"success","data"=>$data], 200);
    }

    public function create(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'service_provider_id' => 'required',
            'length' => 'required|numeric',
            'starting_no'=>'required|numeric',
            'ending_no'=>'required|numeric',
            // 'prefix'=>'required',
            // 'suffix'=>'required',
        ]);

        if($validator->fails()) 
        {
            $log = ['success' => 0, 'errors' => $validator->getMessageBag()->toArray()];
            return response()->json($log, 400);
        }
        $dsp = ServiceProvider::find($request->service_provider_id);
        $filename = Helper::ref_number("S",20,"");
        $data = new SeriesCollection;
        $data->account_id = Auth::user()->account->id;
        $data->service_provider_id = $dsp->id;
        $data->company_code = $dsp->company_code;
        $data->length = $request->length;
        $data->starting_no = $request->starting_no;
        $data->ending_no = $request->ending_no;
        $data->prefix = $request->prefix;
        $data->suffix = $request->suffix;
        $data->total = $request->ending_no - $request->starting_no + 1;
        $data->save();

        return response()->json(['success' => true]);
    }
}
