<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

use App\Models\Series;
use App\Models\SeriesUpload;
use App\Models\Account;

use Auth;
use Log;
use Helper;
use Validator;

class SeriesUploadController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('series_upload/index');
    }

    public function list(Request $request)
    {
        $start_date = $request->start_date ? date('Y-m-d', strtotime($request->start_date)) : "";
        $end_date =  $request->end_date ? date('Y-m-d', strtotime($request->end_date)) : "";

        $data = SeriesUpload::with(["ServiceProvider"])->when($request->sort_by, function ($query, $value) {
            $query->orderBy($value, request('order_by', 'asc'));
        })
        ->when(!isset($request->sort_by), function ($query) {
            $query->latest();
        })
        ->when($request->search, function ($query, $value) {
            $query->where('filename', 'LIKE', '%'.$value.'%')
            ->orWhere("original_filename",'LIKE','%'.$value.'%');
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
            'file' => 'required|mimes:csv,txt,json|max:10000000'
        ]);

        if($validator->fails()) 
        {
            $log = ['success' => 0, 'errors' => $validator->getMessageBag()->toArray()];
            return response()->json($log, 400);
        }

        $file = $request->file('file');

        // Get User Id
        $auth = Auth::user();
        $account = Account::where('user_id', $auth->id)->first();

        // Only manager can upload series
        // if(!$auth->hasRole('manager'))
        // {
        //     $log = array('success' => false, 'errors' => array(
        //         'message' => 'Invalid Action. The uploading of series can only be made by manager.'
        //     ));
        //     Log::error(json_encode($log));
        //     return response()->json($log);
        // }

        $filename = Helper::ref_number("S",20,"");
        $upload = new SeriesUpload;
        $upload->account_id = $account->id;
        $upload->service_provider_id = $request->service_provider_id;
        $upload->original_filename = $file->getClientOriginalName();
        $upload->filename = $filename;
        $upload->save();

        FileController::fileUpload($filename,$upload->id,"series_upload",$file,false);

        Log::info('[SeriesUpload/Create]: File Name : '. $filename);

        return response()->json(['success' => true]);
    }
}
