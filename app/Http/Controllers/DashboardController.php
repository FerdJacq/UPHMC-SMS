<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

use App\Models\ServiceProvider;
use App\Models\Transaction;
use App\Models\TransactionDetails;
use App\Models\TransactionSummary;
use App\Models\Region;
use App\Models\Seller;

use Helper;
use DB;
use Log;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return Inertia::render('dashboard');
    }

    public function dashboard(Request $request){
        $now = date('Y-m-d');
        // $start_date = Carbon::now()->subDays(6);
        // $end_date = Carbon::now();
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $dsp_role = Helper::auth_role_dsp();
        $dsp_list = $dsp_role ? Helper::auth_dsp_list("service_provider_id") : null;
        $seller = DB::table("sellers")->where("tin","029203920132")->first();

        $selected_sellers = collect($request->seller)->map(function ($item) {
            return $item["id"];
        });

        Log::info($selected_sellers);


        $summary=DB::table('transaction_summaries as ts')
        ->join('service_providers as sp', 'ts.service_provider_id', '=', 'sp.id')
        ->join('sellers as s', 'ts.seller_id', '=', 's.id')
        // ->where("assigned_date",$now)
        ->whereBetween('assigned_date', [$start_date, $end_date])
        ->when($dsp_role,function($query) use($dsp_list) {
            $query->whereIn("ts.service_provider_id",$dsp_list);
        })
        ->when($request->type!="all",function($query) use($request) {
            $query->where("ts.type",strtoupper($request->type));
        })
        ->when($request->selected_regions,function($query) use($request){
            $query->whereIn("ts.region_code",$request->selected_regions);
         })
         ->when(Helper::auth_role_seller(),function($query) use($seller){
             $seller_id = ($seller) ? $seller->id : 0;
           $query->whereIn("ts.service_provider_id",Helper::auth_dsp_list("service_provider_id"))
            ->where("seller_id",$seller_id);
        })
        ->when(Helper::auth_role_rdo(),function($query) use($seller){
            $query->whereIn("ts.region_code",Helper::auth_region_list());
         })
         ->when($request->vat_type!="all",function($query) use($request) {
            $query->where("ts.vat_type",strtoupper($request->vat_type));
        })
        ->when($request->seller_type!="all",function($query) use($request) {
            $query->where("s.type",strtoupper($request->seller_type));
        })
        
        ->when($request->selected_dsp,function($query) use($request) {
            $query->whereIn("sp.id",$request->selected_dsp);
        })
        ->when($request->eligible_witheld_seller,function($query) use($request) {
            $query->whereIn("s.eligible_witheld_seller",$request->eligible_witheld_seller);
        })
        ->when(count($selected_sellers),function($query) use($request, $selected_sellers) {
            $query->whereIn("s.id",$selected_sellers);
        })
        ->selectRaw('sp.id,sp.company_name,concat("image/service_provider/",sp.reference_number) as logo,
            transaction_fee,service_fee,commission_fee,
            online_platform_vat,shipping_vat,item_vat,withholding_tax,tax,
            pending,ongoing,delivered,cancelled,refunded,completed,
            (pending+ongoing+delivered+cancelled+refunded+completed) as total
        ')
        ->get();

        $dsp_count = $dsp_role ? [] : DB::table('service_providers')->groupBy('status')->selectRaw('status,count(*) as total')->get();

        // $transactions = Transaction::whereDate("updated_at",$now)->latest()->take(5)->get();
        // $transactions = Transaction::latest()->take(5)->get();
        // $transactions = DB::table("transactions as t")
        // ->join('service_providers as sp', 't.service_provider_id', '=', 'sp.id')
        // ->join('sellers as s', 't.seller_id', '=', 's.id')
        // ->selectRaw('t.*,sp.id as sp_id,sp.company_name,concat("image/service_provider/",sp.reference_number) as logo')
        // ->when($dsp_role,function($query) use($dsp_list) {
        //     $query->whereIn("t.service_provider_id",$dsp_list);
        // })
        //  ->when(Helper::auth_role_seller(),function($query) use($seller){
        //      $seller_id = ($seller) ? $seller->id : 0;
        //    $query->whereIn("t.service_provider_id",Helper::auth_dsp_list("service_provider_id"))
        //     ->where("seller_id",$seller_id);
        // })
        // ->when($request->type!="all",function($query) use($request) {
        //     $query->where("t.type",strtoupper($request->type));
        // })
        // ->when($request->vat_type!="all",function($query) use($request) {
        //     $query->where("t.vat_type",strtoupper($request->vat_type));
        // })
        // ->when($request->selected_regions,function($query) use($request){
        //     $query->whereIn("t.region_code",$request->selected_regions);
        //  })
        // ->when(Helper::auth_role_rdo(),function($query) use($seller){
        //     $query->whereIn("t.region_code",Helper::auth_region_list());
        //  })
        // ->when($request->seller_type!="all",function($query) use($request) {
        //     $query->where("s.type",strtoupper($request->seller_type));
        // })
        // ->when($request->selected_dsp,function($query) use($request) {
        //     $query->whereIn("sp.id",$request->selected_dsp);
        // })
        // ->when($request->eligible_witheld_seller,function($query) use($request) {
        //     $query->whereIn("s.eligible_witheld_seller",$request->eligible_witheld_seller);
        // })
        // ->when(count($selected_sellers),function($query) use($request, $selected_sellers) {
        //     $query->whereIn("t.id",$selected_sellers);
        // })
        // ->orderBy('t.id', 'DESC')->take(6)->get();
        
        $transactions = [];

        $top_sellers = DB::table("transaction_summaries as ts")
        ->join('service_providers as sp', 'ts.service_provider_id', '=', 'sp.id')
        ->join('sellers as s', 'ts.seller_id', '=', 's.id')
        ->selectRaw('
            s.registered_name,
            s.tin,
            s.type,
            s.has_cor,
            s.eligible_witheld_seller,
            SUM(ts.base_price) as base_price
        ')
        ->when($dsp_role,function($query) use($dsp_list) {
            $query->whereIn("ts.service_provider_id",$dsp_list);
        })
         ->when(Helper::auth_role_seller(),function($query) use($seller){
             $seller_id = ($seller) ? $seller->id : 0;
           $query->whereIn("ts.service_provider_id",Helper::auth_dsp_list("service_provider_id"))
            ->where("seller_id",$seller_id);
        })
        ->when(Helper::auth_role_rdo(),function($query) use($seller){
            $query->whereIn("ts.region_code",Helper::auth_region_list());
         })
         ->when($request->selected_regions,function($query) use($request){
            $query->whereIn("ts.region_code",$request->selected_regions);
         })
         ->when($request->type!="all",function($query) use($request) {
            $query->where("ts.type",strtoupper($request->type));
        })
        ->when($request->vat_type!="all",function($query) use($request) {
            $query->where("ts.vat_type",strtoupper($request->vat_type));
        })
        ->when($request->seller_type!="all",function($query) use($request) {
            $query->where("s.type",strtoupper($request->seller_type));
        })
        ->when($request->selected_dsp,function($query) use($request) {
            $query->whereIn("sp.id",$request->selected_dsp);
        })
        ->when($request->eligible_witheld_seller,function($query) use($request) {
            $query->whereIn("s.eligible_witheld_seller",$request->eligible_witheld_seller);
        })
        ->when(count($selected_sellers),function($query) use($request, $selected_sellers) {
            $query->whereIn("s.id",$selected_sellers);
        })
        ->whereBetween('assigned_date', [$start_date, $end_date])
        ->groupBy("registered_name")
        ->orderBy('base_price', 'DESC')
        ->take(10)->get();

         // $start_date = Carbon::now()->subDays(6);
        // $end_date = Carbon::now();
        $start_date2 = $start_date;
        if ($start_date==$end_date) $start_date2 = Carbon::parse($start_date2)->subDays(6);

        $statistics = DB::table('transaction_summaries as ts')
        ->leftJoin('service_providers as sp','ts.service_provider_id','sp.id')
        ->leftJoin('regions as r', 'r.region_code', '=', 'ts.region_code')
        ->join('sellers as s', 'ts.seller_id', '=', 's.id')
        ->when($dsp_role,function($query) use($dsp_list) {
            $query->whereIn("ts.service_provider_id",$dsp_list);
        })
         ->when(Helper::auth_role_seller(),function($query) use($seller){
             $seller_id = ($seller) ? $seller->id : 0;
           $query->whereIn("ts.service_provider_id",Helper::auth_dsp_list("service_provider_id"))
            ->where("seller_id",$seller_id);
        })
        ->when(Helper::auth_role_rdo(),function($query) use($seller){
            $query->whereIn("ts.region_code",Helper::auth_region_list());
         })
         ->when($request->selected_regions,function($query) use($request){
            $query->whereIn("ts.region_code",$request->selected_regions);
         })
         ->when($request->type!="all",function($query) use($request) {
            $query->where("ts.type",strtoupper($request->type));
        })
        ->when($request->vat_type!="all",function($query) use($request) {
            $query->where("ts.vat_type",strtoupper($request->vat_type));
        })
        ->when($request->seller_type!="all",function($query) use($request) {
            $query->where("s.type",strtoupper($request->seller_type));
        })
        ->when($request->selected_dsp,function($query) use($request) {
            $query->whereIn("sp.id",$request->selected_dsp);
        })
        ->when($request->eligible_witheld_seller,function($query) use($request) {
            $query->whereIn("s.eligible_witheld_seller",$request->eligible_witheld_seller);
        })
        ->when(count($selected_sellers),function($query) use($request, $selected_sellers) {
            $query->whereIn("s.id",$selected_sellers);
        })
        ->whereBetween('assigned_date', [$start_date2, $end_date])
        ->selectRaw('date(assigned_date) as date,r.name as region_name,r.region_code as region_code,UNIX_TIMESTAMP(assigned_date) as timestamp, sum(tax) as total')
        ->groupBy(DB::raw('region_name,region_code,date'))
        ->orderBy('date', 'ASC')
        ->orderBy('region_code', 'ASC')
        ->get();

        $sellers =  DB::table("sellers as s")
        ->when($request->seller_type!="all",function($query) use($request) {
            $query->where("type",strtoupper($request->seller_type));
        })
        ->when($request->eligible_witheld_seller,function($query) use($request) {
            $query->whereIn("eligible_witheld_seller",$request->eligible_witheld_seller);
        })
        ->when(Helper::auth_role_rdo(),function($query){
            $query->whereIn("region_code",Helper::auth_region_list());
         })
        ->when($request->selected_regions,function($query) use($request){
            $query->whereIn("region_code",$request->selected_regions);
         })
         ->when(count($selected_sellers),function($query) use($request, $selected_sellers) {
            $query->whereIn("s.id",$selected_sellers);
        })
        ->selectRaw('
            sum(if(eligible_witheld_seller = "NONE", 1, 0)) as non_witheld,
            sum(if(eligible_witheld_seller = "ELIGIBLE", 1, 0)) as eligible_witheld,
            sum(if(eligible_witheld_seller = "ACTIVE", 1, 0)) as active_witheld
        ')
        ->first();

        $sub_filter =  DB::table("sellers as s")
        ->join('transaction_summaries as t', 't.seller_id', '=', 's.id')
        ->when($request->seller_type!="all",function($query) use($request) {
            $query->where("s.type",strtoupper($request->seller_type));
        })
        ->when($request->type!="all",function($query) use($request) {
            $query->where("t.type",strtoupper($request->type));
        })
        ->when($request->vat_type!="all",function($query) use($request) {
            $query->where("t.vat_type",strtoupper($request->vat_type));
        })
        ->when($request->seller_type!="all",function($query) use($request) {
            $query->where("s.type",strtoupper($request->seller_type));
        })
        ->when($request->selected_dsp,function($query) use($request) {
            $query->whereIn("t.service_provider_id",$request->selected_dsp);
        })
        ->when($request->eligible_witheld_seller,function($query) use($request) {
            $query->whereIn("eligible_witheld_seller",$request->eligible_witheld_seller);
        })
        ->when(Helper::auth_role_rdo(),function($query){
            $query->whereIn("s.region_code",Helper::auth_region_list());
         })
        ->when($request->selected_regions,function($query) use($request){
            $query->whereIn("s.region_code",$request->selected_regions);
         })
        ->when(count($selected_sellers),function($query) use($request, $selected_sellers) {
            $query->whereIn("s.id",$selected_sellers);
        })
        ->whereBetween('assigned_date', [$start_date, $end_date])
        ->select(
            DB::raw('sum(if(eligible_witheld_seller = "NONE", t.base_price, 0)) as non_witheld'),
            DB::raw('sum(if(eligible_witheld_seller = "ELIGIBLE", t.base_price, 0)) as eligible_witheld'),
            DB::raw('sum(if(eligible_witheld_seller = "ACTIVE", t.base_price, 0)) as active_witheld')
        )
        ->first();


        $data = [
            "service_providers"=>$summary,
            "dsp_count"=>$dsp_count,
            "now"=>$now,
            "transactions"=>$transactions,
            "top_sellers"=>$top_sellers,
            "statistics"=>collect($statistics)->sortBy("date")->values(),
            "sellers"=>$sellers,
            "sellers_baseprice"=>$sub_filter
        ];
        return response()->json(["status"=>"success","data"=>$data], 200);
    }

    public function seller(Request $request)
    {
        // $data = Seller::where('registered_name', 'LIKE', '%'.$request->search.'%')
        // ->orWhere("business_name",'LIKE','%'.$request->search.'%')
        // ->orWhere("tin",'LIKE','%'.$request->search.'%')
        // ->get();

        $data = Seller::get();

        return response()->json(["status"=>"success","data"=>$data], 200);
    }

    //for detailed query only sample - 5/15/2023
    public function dashboard2()
    {
        $now = date('Y-m-d');
        $dsp_role = Helper::auth_role_dsp();
        $dsp_list = $dsp_role ? Helper::auth_dsp_list("service_provider_id") : null;

        $service_providers = DB::table('transactions as p')
        ->join('service_providers as sp', 'p.service_provider_id', '=', 'sp.id')
        ->where(function($query) use ($now){
            $query
            ->whereRaw("date(p.created_at)=?",$now)
            ->orwhereRaw("date(ongoing_date)=?",$now)
            ->orwhereRaw("date(delivered_date)=?",$now)
            ->orwhereRaw("date(completed_date)=?",$now)
            ->orwhereRaw("date(cancelled_date)=?",$now)
            ->orwhereRaw("date(refunded_date)=?",$now);
        })
        ->when($dsp_role,function($query) use($dsp_list) {
            $query->whereIn("sp.service_provider_id",$dsp_list);
        })
        ->when($request->type!="all",function($query) use($dsp_list) {
            $query->where("p.type",strtoupper($request->type));
        })
        ->when(Helper::auth_role_rdo(),function($query) use($seller){
            $query->whereIn("region_code",Helper::auth_region_list());
         })
        ->where("p.status","PENDING")
        ->groupBy("p.status","sp.id",'sp.company_name','sp.code','sp.reference_number','transaction_fee','service_fee','commission_fee','p.tax')
        ->selectRaw('sp.id,sp.company_name,concat("image/service_provider/",sp.reference_number) as logo,sp.status,p.status,count(p.id) as total,
            sum(p.transaction_fee) as transaction_fee, sum(p.service_fee) as service_fee, sum(p.commission_fee) as commission_fee, sum(p.tax) as tax')
        ->get();

        $dsp_count = DB::table('service_providers')->groupBy('status')->selectRaw('status,count(*) as total')->get();

        $data = [
            "service_providers"=>$service_providers,
            "dsp_count"=>$dsp_count,
            "now"=>$now
        ];
        return response()->json(["status"=>"success","data"=>$data], 200);

    }

    public function regions(Request $request)
    {
        $data = Region::when(Helper::auth_role_rdo(),function($query){
            $query->whereIn("region_code",Helper::auth_region_list());
         })
         ->get();   
        return response()->json(["status"=>"success","data"=>$data], 200);
    }
}
