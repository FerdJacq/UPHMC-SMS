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


class SeriesController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('series/index');
    }

    public function list(Request $request)
    {
        $start_date = $request->start_date ? date('Y-m-d', strtotime($request->start_date)) : "";
        $end_date =  $request->end_date ? date('Y-m-d', strtotime($request->end_date)) : "";

        $data = Series::with(["ServiceProvider"])->when($request->sort_by, function ($query, $value) {
            $query->orderBy($value, request('order_by', 'asc'));
        })
        ->when(!isset($request->sort_by), function ($query) {
            $query->latest();
        })
        ->when($request->search, function ($query, $value) {
            $query->where('prefix', 'LIKE', '%'.$value.'%')
            ->orWhere("series_no",'LIKE','%'.$value.'%')
            ->orWhere("complete_no",'LIKE','%'.$value.'%');
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
}
