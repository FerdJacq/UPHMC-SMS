<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

use App\Models\ServiceProvider;
use App\Models\Transaction;
use App\Models\TransactionDetails;

use App\Models\Seller;

use Illuminate\Support\Carbon;

use Auth;
use Helper;
use DB;
use Excel;


use App\Exports\TransactionExport;
use App\Exports\SellersSummaryExport;

class SellerController extends Controller
{
    //
    public function index()
    {
        return Inertia::render('seller/index');
    }
    

    public function list(Request $request)
    {
        $date_type = ($request->selected_date_type=="PENDING") ? "created_at" : strtolower($request->selected_date_type."_date");
        $start_date = $request->start_date ? date('Y-m-d', strtotime($request->start_date)) : date('Y-m-d');
        $end_date =  $request->end_date ? date('Y-m-d', strtotime($request->end_date)) :  date('Y-m-d');
        $eligible_witheld_seller = ($request->eligible_witheld_seller) ? $request->eligible_witheld_seller : [];

        $data = Seller::with(["serviceProvider"])
        // ->leftJoin('transactions as t', function($join) use ($start_date,$end_date,$request)
        // {
        //     $join->on('t.seller_id', '=', 'sellers.id')
        //     ->when(Helper::auth_role_dsp(),function($query){
        //         $query->whereIn("t.service_provider_id",Helper::auth_dsp_list("service_provider_id"));
        //     })
        //     // ->when(Helper::auth_role_rdo(),function($query){
        //     //     $query->whereIn("t.region_code",Helper::auth_region_list());
        //     // })
        //     ->where("t.status","COMPLETED")
        //     ->when(in_array($request->selected_type,[1,2,3]),function($q) use ($start_date,$end_date,$request){
        //         if ($start_date && $end_date)
        //         {
        //             if($request->selected_type==1)
        //                 $q->whereRaw("date(t.remitted_date) between ? and ?",[$start_date,$end_date]);
        //             else
        //                 $q->whereRaw("date(t.completed_date) between ? and ?",[$start_date,$end_date]);
        //         }
        //     });
        // })
        ->when($request->sort_by, function ($query, $value) {
            $query->orderBy("sellers.".$value, request('order_by', 'asc'));
        })
        ->when(Helper::auth_role_dsp(),function($query){
            $query->whereHas('serviceProvider', function ($q) {
                $q->whereIn("service_provider_id",Helper::auth_dsp_list("service_provider_id"));
            });
        })
        // ->whereIn("eligible_witheld_seller",$eligible_witheld_seller)
        ->when($eligible_witheld_seller, function ($query, $value) {
            $query->whereIn("eligible_witheld_seller",$value);
        })
        ->when($request->has_cor=="WCOR", function ($query, $value) {
            $query->where("has_cor",1);
        })
        ->when($request->has_cor=="WOCOR", function ($query, $value) {
            $query->where("has_cor",0);
        })
       ->where(function($query) use ($request){
            $search = $request->search;
            if($search)
                $query->where('registered_name', 'LIKE', '%'.$search.'%')
                ->orWhere("business_name",'LIKE','%'.$search.'%')
                ->orWhere("tin",'LIKE','%'.$search.'%');
        })
        // ->when(in_array($request->selected_type,[1,2]),function($q) use ($request){
        //     if($request->selected_type==1)
        //         $q->whereNotNull("remitted_date");
        //     else
        //         $q->whereNull("remitted_date");
        // })
        // ->where("t.status","COMPLETED")
        // ->whereRaw("date(t.completed_date) between ? and ?",[$start_date,$end_date])
        ->when(Helper::auth_role_rdo(),function($query){
            $query->whereIn("sellers.region_code",Helper::auth_region_list());
        })
        // ->groupBy("sellers.id","registered_name", "registered_address", "business_name", "tin", "sellers.vat_type", "sellers.contact_number", "sellers.email")
        // ->select(
        //     'sellers.id',
        //     'sellers.registered_name',
        //     'sellers.registered_address',
        //     'sellers.business_name',
        //     'sellers.tin',
        //     'sellers.vat_type',
        //     'sellers.contact_number',
        //     'sellers.email',
        //     'sellers.eligible_witheld_seller',
        //     'sellers.sales_per_anum',
        //     'sellers.type',
        //     'sellers.has_cor',
        //     'sellers.created_at',
        //     'sellers.updated_at',
        //     DB::raw('SUM(t.online_platform_vat) as online_platform_vat'),
        //     DB::raw('SUM(t.shipping_vat) as shipping_vat'),
        //     DB::raw('SUM(t.item_vat) as item_vat'),
        //     DB::raw('SUM(t.base_price) as base_price'),
        //     DB::raw('SUM(t.tax) as tax'),
        //     DB::raw('SUM(t.withholding_tax) as withholding_tax'),
        // )
        // ->selectRaw('count(sellers.id) as id,sellers.registered_name, registered_address,sellers.business_name,sellers.tin,sellers.vat_type,sellers.contact_number,sellers.email,sellers.created_at,sellers.updated_at,
        // sum(t.online_platform_vat) as online_platform_vat,sum(t.shipping_vat) as shipping_vat,sum(t.item_vat) as item_vat,sum(t.base_price) as base_price,sum(t.tax) as tax,sum(t.withholding_tax) as withholding_tax')
        ->orderByDesc("sellers.id")
        // ->toSql();
        ->paginate($request->page_size ?? 10);

        $sellers =  DB::table("sellers")
        ->when(Helper::auth_role_rdo(),function($query){
            $query->whereIn("region_code",Helper::auth_region_list());
         })
        ->selectRaw('
            sum(if(eligible_witheld_seller = "NONE", 1, 0)) as NONE,
            sum(if(eligible_witheld_seller = "ELIGIBLE", 1, 0)) as ELIGIBLE,
            sum(if(eligible_witheld_seller = "ACTIVE", 1, 0)) as ACTIVE
        ')
        ->first();

        $sub_filters =  DB::table("sellers")
        ->when(Helper::auth_role_rdo(),function($query){
            $query->whereIn("region_code",Helper::auth_region_list());
         })
         ->when($eligible_witheld_seller, function ($query, $value) {
            $query->whereIn("eligible_witheld_seller",$value);
        })
        ->selectRaw('
            sum(if(has_cor = 1, 1, 0)) as WCOR,
            sum(if(has_cor = 0, 1, 0)) as WOCOR
        ')
        ->first();

        $sellers = [
            "NONE"=>$sellers->NONE,
            "ELIGIBLE"=>$sellers->ELIGIBLE,
            "ACTIVE"=>$sellers->ACTIVE,
            "WCOR"=>$sub_filters->WCOR,
            "WOCOR"=>$sub_filters->WOCOR
        ];

        return response()->json(["status"=>"success","data"=>$data,"sellers"=>$sellers], 200);
    }

    public function get($id)
    {
        $data = Seller::with(["serviceProvider"])->where("id",$id)->first();
        return response()->json(["status"=>"success","data"=>$data], 200);
    }

    public function transactions(Request $request)
    {   
        // $request->selected_type = 3;
        // if ($request->selected_date_type=="REMITTED"){
        //     $request->selected_type=1;
        // }
        // else if ($request->selected_date_type=="UNREMITTED"){
        //     $request->selected_type=2;
        //     $request->selected_date_type="COMPLETED";
        // };
        $date_type = strtolower($request->selected_date_type);
        $start_date = $request->start_date ? date('Y-m-d', strtotime($request->start_date)) : "";
        $end_date =  $request->end_date ? date('Y-m-d', strtotime($request->end_date)) : "";
        $search = $request->search;

        $data = Transaction::with(['ServiceProvider'])
        ->where("seller_id",$request->seller_id)
        ->where(function($query) use ($search) {
            if ($search)
            {
                $query->where('trans_id', 'LIKE', '%'.$search.'%')
                ->orWhere("or_number",'LIKE','%'.$search.'%')
                ->orWhere("reference_number",'LIKE','%'.$search.'%');
            }
        })
        ->when($request->selected_date_type && $start_date && $end_date, function ($query, $value) use($date_type,$start_date,$end_date) {
            $query->whereBetween(DB::raw('DATE('.$date_type.')'), [$start_date, $end_date]);
        })
        ->when(Helper::auth_role_rdo(),function($query){
            $query->whereIn("region_code",Helper::auth_region_list());
        })
        ->when(Helper::auth_role_dsp(),function($query){
            $query->whereIn("service_provider_id",Helper::auth_dsp_list("service_provider_id"));
        })
        ->when($request->selected_dsp,function($query) use($request){
            $query->whereIn("service_provider_id",$request->selected_dsp);
        })
        ->when($request->status,function($query) use($request){
            $query->whereIn("status",$request->status);
        })
        ->when(in_array($request->selected_type,[1,2]),function($q) use ($request){
            if($request->selected_type==1)
                $q->whereNotNull("remitted_date");
            else
                $q->whereNull("remitted_date");
        })
        ->when($request->sort_by, function ($query, $value) {
            $query->orderBy($value, request('order_by', 'asc'));
        })
        ->when(!isset($request->sort_by), function ($query) {
            $query->latest();
        })
        ->orderByDesc("id")
        ->paginate($request->page_size ?? 10);

        $summary = $this->summary_transaction($request,$search,$date_type,$start_date,$end_date);

        return response()->json(["status"=>"success","data"=>$data,"summary"=>$summary], 200);
    }

    private function summary_transaction($request,$search,$date_type,$start_date,$end_date){
        
        $date_type = "t.".$date_type;
        $summary=DB::table('transactions as t')
        ->join('service_providers as sp', 't.service_provider_id', '=', 'sp.id')
        ->where(function($query) use ($search) {
            if ($search)
            {
                $query->where('t.trans_id', 'LIKE', '%'.$search.'%')
                ->orWhere("t.or_number",'LIKE','%'.$search.'%')
                ->orWhere("t.reference_number",'LIKE','%'.$search.'%');
            }
        })
        ->when($request->seller_id,function($query) use($request){
            $query->where("seller_id",$request->seller_id);
        })
        ->when($request->selected_date_type, function ($query, $value) use($date_type,$start_date,$end_date) {
            if($start_date && $end_date) $query->whereBetween(DB::raw('DATE('.$date_type.')'), [$start_date, $end_date]);
        })
        ->when(Helper::auth_role_dsp(),function($query){
            $query->whereIn("t.service_provider_id",Helper::auth_dsp_list("service_provider_id"));
        })
        ->when($request->transaction_status,function($query) use($request){
            $query->whereIn("t.status",$request->transaction_status);
        })
        ->when($request->selected_dsp,function($query) use($request){
            $query->whereIn("t.service_provider_id",$request->selected_dsp);
        })
        ->when(in_array($request->selected_type,[1,2]),function($q) use ($request){
            if($request->selected_type==1)
                $q->whereNotNull("t.remitted_date");
            else
                $q->whereNull("t.remitted_date");
        })
        ->groupByRaw('sp.company_name')
        ->selectRaw('
            sp.company_name,
            concat("image/service_provider/",sp.reference_number) as logo,
            sum(if(t.status = "COMPLETED", base_price, 0)) as base_price,
            sum(if(t.status = "COMPLETED", transaction_fee, 0)) as transaction_fee,
            sum(if(t.status = "COMPLETED", service_fee, 0)) as service_fee,
            sum(if(t.status = "COMPLETED", commission_fee, 0)) as commission_fee,
            sum(if(t.status = "COMPLETED", online_platform_vat, 0)) as online_platform_vat,
            sum(if(t.status = "COMPLETED", shipping_vat, 0)) as shipping_vat,
            sum(if(t.status = "COMPLETED", item_vat, 0)) as item_vat,
            sum(if(t.status = "COMPLETED", withholding_tax, 0)) as withholding_tax,
            sum(if(t.status = "COMPLETED", total_amount, 0)) as total_amount,
            sum(if(t.status = "COMPLETED", tax, 0)) as tax,
            SUM(if(t.status = "PENDING", 1, 0)) as pending,
            SUM(if(t.status = "ONGOING", 1, 0)) as ongoing,
            SUM(if(t.status = "DELIVERED", 1, 0)) as delivered,
            SUM(if(t.status = "COMPLETED", 1, 0)) as completed,
            SUM(if(t.status = "CANCELLED", 1, 0)) as cancelled,
            SUM(if(t.status = "REFUNDED", 1, 0)) as refunded
        ')
        ->orderByRaw("base_price desc,company_name")
        ->get();

        return $summary;
    }

    public function generate_report(Request $request)
    {
        $data = $this->validate($request, [
            'start_date' => 'required',
            'end_date' => 'required',
            'selected_date_type' => 'required|string',
            "seller_id"=>"required"
        ]);

        $date_type = strtolower($request->selected_date_type."_date");
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $ref = Helper::ref_number("SE",20,"-");
        $filename = 'excel/'.$ref.'-seller-transaction.xlsx';
        $params = new TransactionExport($request);
        

        Excel::store($params, $filename ,'storage');
    
        return response()->json(['file' => $filename]);
    }

    public function generate_summary_report(Request $request)
    {
        $data = $this->validate($request, [
            'start_date' => 'required',
            'end_date' => 'required'
        ]);

        $date_type = strtolower($request->selected_date_type."_date");
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $ref = Helper::ref_number("SE",20,"-");
        $filename = 'excel/'.$ref.'-sellers-summary.xlsx';
        $params = new SellersSummaryExport($request);
        

        Excel::store($params, $filename ,'storage');
    
        return response()->json(['file' => $filename]);
    }
}
