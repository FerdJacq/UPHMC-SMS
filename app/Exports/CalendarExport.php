<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;


use App\Exports\Sheets\CalendarPerDspPerRegionSheet;
use App\Exports\Sheets\CalendarPerRegionPerDaySheet;
use App\Exports\Sheets\CalendarPerDspSheet;
use App\Exports\Sheets\CalendarPerDspPerDaySheet;
use App\Exports\Sheets\CalendarPerRegionSheet;
use App\Exports\Sheets\SellerInformationSheet;


use Helper;
use DB;

class CalendarExport implements WithMultipleSheets
{
    use Exportable;

    protected $request;
    
    public function __construct($request)
    {
        $this->request = $request;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];
        $request = $this->request;
        $startDate = $request->start_date ? $request->start_date : date('Y-m-d');
        $endDate = $request->last_date ? $request->last_date : date('Y-m-d');
        $selected_dsp = $request->selected_dsp ? $request->selected_dsp :[];
        $selected_regions = $request->selected_regions ? $request->selected_regions :[];
        $dsp_role = Helper::auth_role_dsp();
        $dsp_list = $dsp_role ? Helper::auth_dsp_list("service_provider_id") : null;
        $seller = DB::table("sellers")->where("tin","029203920132")->first();

        $summaries=DB::table('service_providers as sp')
        ->leftJoin('transaction_summaries as ts', function($join) use($startDate,$endDate)
        {
            $join->on('ts.service_provider_id', '=', 'sp.id')
            ->whereBetween('ts.assigned_date',[$startDate,$endDate]);
        })
        ->leftJoin('regions as r', 'r.region_code', '=', 'ts.region_code')
        ->leftJoin('sellers as s', 's.id', 'ts.seller_id')
        ->when($request->seller_id,function($query) use($request){
            $query->where("seller_id",$request->seller_id);
        })
        ->where(function ($query) use($startDate,$endDate) {
            $query->whereBetween('ts.assigned_date', [$startDate, $endDate])
            ->orWhereNull('ts.assigned_date');
        })
        ->where(function ($query) use($selected_regions) {
            if(in_array("",$selected_regions))
                $query->where('ts.region_code','')
                ->orWhereIn("ts.region_code",$selected_regions)
                ->orWhereNull('ts.region_code');
            else
                $query->whereIn("ts.region_code",$selected_regions);
        })
        ->when($dsp_role,function($query) use($dsp_list) {
            $query->whereIn("sp.id",$dsp_list);
        })
        ->when(Helper::auth_role_seller(),function($query) use($seller){
             $seller_id = ($seller) ? $seller->id : 0;
           $query->whereIn("service_provider_id",Helper::auth_dsp_list("service_provider_id"))
            ->where("seller_id",$seller_id);
        })
        ->when(Helper::auth_role_rdo(),function($query){
            $query->whereIn("r.region_code",Helper::auth_region_list());
        })
        ->when($selected_dsp,function($query) use($selected_dsp) {
            $query->whereIn("sp.id",$selected_dsp);
        })
        ->when($request->transaction_type!="all" && $request->seller_type, function ($query) use($request) {
            $query->where("ts.type",strtoupper($request->transaction_type));
        })
        ->when($request->seller_type!="all" && $request->seller_type, function ($query) use($request) {
            $query->where("s.type",strtoupper($request->seller_type));
        })
        ->when($request->eligible_witheld_seller,function($query) use($request) {
            $query->whereIn("eligible_witheld_seller",$request->eligible_witheld_seller);
        })
        ->selectRaw('sp.id,sp.company_name,concat("image/service_provider/",sp.reference_number) as logo,
            r.name as region_name,r.region_desc as region_desc,ts.region_code,
            transaction_fee,service_fee,commission_fee,base_price,total_amount,
            online_platform_vat,shipping_vat,item_vat,withholding_tax,tax,
            pending,ongoing,delivered,cancelled,refunded,completed,
            (pending+ongoing+delivered+cancelled+refunded+completed) as total,assigned_date
        ')
        ->orderBy("assigned_date")
        ->orderBy("company_name")
        ->get();

        $data = [
            "summaries"=>$summaries,
            "start_date"=>$startDate,
            "end_date"=>$endDate,
            "seller_id"=>$request->seller_id
        ];

        if ($request->seller_id){
            $sheets[] = new SellerInformationSheet($request,"SELLER INFORMATION");
        }
        $sheets[] = new CalendarPerDspSheet($data,"DIGITAL SERVICE PROVIDER");
        $sheets[] = new CalendarPerRegionSheet($data,"PER REGION");
        $sheets[] = new CalendarPerDspPerRegionSheet($data,"DSP PER REGION");
        $sheets[] = new CalendarPerDspPerDaySheet($data,"DSP PER DAY");
        $sheets[] = new CalendarPerRegionPerDaySheet($data,"DSP PER REGION PER DAY");
        // $sheets[] = new TransactionSheet($this->request,"DATA");

        return $sheets;
    }
}
