<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransactionSummary;
use App\Models\SellerServiceProvider;
use App\Models\Transaction;
use App\Models\TransactionLogs;


use DB;
use Helper;

class SummaryController extends Controller
{
    //

    public function daily_count()
    {
        $now = date('Y-m-d');
        $dsp_role = Helper::auth_role_dsp();
        $dsp_list = $dsp_role ? Helper::auth_dsp_list("service_provider_id") : null;
        
        $seller = DB::table("sellers")->where("tin","029203920132")->first();
        $data = TransactionSummary::select(
            DB::raw("SUM(pending) as pending"),
            DB::raw("SUM(ongoing) as ongoing"),
            DB::raw("SUM(delivered) as delivered"),
            DB::raw("SUM(completed) as completed"),
            DB::raw("SUM(cancelled) as cancelled"),
            DB::raw("SUM(refunded) as refunded"),
            DB::raw("SUM(pending)+SUM(ongoing)+SUM(delivered)+SUM(completed)+SUM(cancelled)+SUM(refunded) as total")
        )
        ->where("assigned_date",$now)
        ->when($dsp_role,function($query) use($dsp_list) {
            $query->whereIn("service_provider_id",$dsp_list);
        })
         ->when(Helper::auth_role_seller(),function($query) use($seller){
             $seller_id = ($seller) ? $seller->id : 0;
           $query->whereIn("service_provider_id",Helper::auth_dsp_list("service_provider_id"))
            ->where("seller_id",$seller_id);
        })
        ->when(Helper::auth_role_rdo(),function($query) use($seller){
            $query->whereIn("region_code",Helper::auth_region_list());
         })
        ->first();

        $online_sellers = DB::table('seller_service_providers as ssp')
        ->join("sellers as s","ssp.seller_id","s.id")
        ->when(Helper::auth_role_dsp(),function($query){
            $query->whereIn("ssp.service_provider_id",Helper::auth_dsp_list("service_provider_id"));
        })
        ->when(Helper::auth_role_seller(),function($query) use($seller){
             $seller_id = ($seller) ? $seller->id : 0;
           $query->whereIn("ssp.service_provider_id",Helper::auth_dsp_list("service_provider_id"))
            ->where("seller_id",$seller_id);
        })
        ->when(Helper::auth_role_rdo(),function($query){
            $query->whereIn("s.region_code",Helper::auth_region_list());
        })
        ->whereRaw("DATE(ssp.created_at)=?",[$now])
        ->groupBy("seller_id")->get();

        $remitted = Transaction::whereNotNull("remitted_date")
        ->where("status","COMPLETED")
        ->whereRaw("DATE(remitted_date)=?",[$now])
        ->when(Helper::auth_role_dsp(),function($query){
            $query->whereIn("service_provider_id",Helper::auth_dsp_list("service_provider_id"));
        })
         ->when(Helper::auth_role_seller(),function($query) use($seller){
             $seller_id = ($seller) ? $seller->id : 0;
           $query->whereIn("service_provider_id",Helper::auth_dsp_list("service_provider_id"))
            ->where("seller_id",$seller_id);
        })
        ->when(Helper::auth_role_rdo(),function($query) use($seller){
            $query->whereIn("region_code",Helper::auth_region_list());
         })
        ->count();

        $unremitted = Transaction::whereNull("remitted_date")
        ->where("status","COMPLETED")
        ->whereRaw("DATE(completed_date)=?",[$now])
        ->when(Helper::auth_role_dsp(),function($query){
            $query->whereIn("service_provider_id",Helper::auth_dsp_list("service_provider_id"));
        })
         ->when(Helper::auth_role_seller(),function($query) use($seller){
             $seller_id = ($seller) ? $seller->id : 0;
           $query->whereIn("service_provider_id",Helper::auth_dsp_list("service_provider_id"))
            ->where("seller_id",$seller_id);
        })
        
        ->when(Helper::auth_role_rdo(),function($query) use($seller){
            $query->whereIn("region_code",Helper::auth_region_list());
         })
        ->count();

        $data->remitted = $remitted;
        $data->unremitted = $unremitted;
        $data->total = $data->total + $data->remitted + $data->unremitted;

        $data->editted = TransactionLogs::where("is_seen",0)->count();


        $data->seller = $online_sellers->count();

        return response()->json(["status"=>"success","data"=>$data], 200);
    }
}
