<?php

namespace App\Exports\Sheets;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromArray ;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStartRow;

use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\BeforeWriting;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

use Log;
use DB;
use Auth;
use Helper;

class TransactionSheet implements FromCollection, WithMapping ,  WithHeadings,  ShouldAutoSize , WithStrictNullComparison , WithTitle , WithStyles , WithCustomStartCell , WithColumnWidths 
{
    protected int $counter = 1;
    protected $request;

    public function __construct($request,$title)
    {
        $this->request = $request;
        $this->title = $title;
    }
    
    public function collection()
    {       
        $request = $this->request;
    
        // $request->transaction_status = (in_array($request->selected_date_type,["REMITTED","UNREMITTED"])) ? ["COMPLETED"] : $request->transaction_status;  
        $date_type = strtolower($request->selected_date_type);
        $start_date =  date('Y-m-d', strtotime($request->start_date));
        $end_date =  date('Y-m-d', strtotime($request->end_date));
        $seller = DB::table("sellers")->where("tin","029203920132")->first();
        
        $query = transaction::with(["ServiceProvider"])
        ->when($request->seller_id,function($query) use($request){
            $query->where("seller_id",$request->seller_id);
        })
        ->when($request->selected_date_type, function ($query, $value) use($date_type,$start_date,$end_date) {
            $query->whereBetween(DB::raw('DATE('.$date_type.')'), [$start_date, $end_date]);
        })
        ->when(Helper::auth_role_dsp(),function($query){
            $query->whereIn("service_provider_id",Helper::auth_dsp_list("service_provider_id"));
        })
        ->when($request->transaction_status,function($query) use($request){
            $query->whereIn("status",$request->transaction_status);
        })
         ->when(Helper::auth_role_seller(),function($query) use($seller){
             $seller_id = ($seller) ? $seller->id : 0;
           $query->whereIn("service_provider_id",Helper::auth_dsp_list("service_provider_id"))
            ->where("seller_id",$seller_id);
        })
        ->when($request->selected_regions,function($query) use($request){
            $query->whereIn("region_code",$request->selected_regions);
        })
        ->when(Helper::auth_role_rdo(),function($query) use($seller){
            $query->whereIn("region_code",Helper::auth_region_list());
        })
        ->when(in_array($request->selected_type,[1,2]),function($q) use ($request){
            if($request->selected_type==1)
                $q->whereNotNull("remitted_date");
            else
                $q->whereNull("remitted_date");
        })
        ->when($request->selected_dsp,function($query) use($request){
            $query->whereIn("service_provider_id",$request->selected_dsp);
        })
        ->get();
        // Log::info($request);
        // Log::info($query);
        // Log::info($date_type);

        return $query;
    }

    public function map($row): array
    {
        // $transaction_fee = in_array($row->status,["DELIVERED","COMPLETED"]) ? $row->transaction_fee : "";
        // $service_fee = in_array($row->status,["DELIVERED","COMPLETED"]) ? $row->service_fee : "";
        // $commission_fee = in_array($row->status,["DELIVERED","COMPLETED"]) ? $row->commission_fee : "";
        // $tax = in_array($row->status,["DELIVERED","COMPLETED"]) ? $row->tax : "";
        $transaction_fee = $row->transaction_fee;
        $service_fee = $row->service_fee;
        $commission_fee =$row->commission_fee;
        $tax = $row->tax;
        $online_platform_vat=$row->online_platform_vat;
        $shipping_vat=$row->shipping_vat;
        $item_vat=$row->item_vat;
        $withholding_tax=$row->withholding_tax;
        $fields = [          
            $row->trans_id,
            $row->reference_number,
            $row->ServiceProvider->company_name,
            $row->total_amount,
            $row->base_price,
            $transaction_fee,
            $service_fee,
            $commission_fee,
            $online_platform_vat,
            $shipping_vat,
            $item_vat,
            $withholding_tax,
            $tax,
            $row->status,
            $row->pending_date,
            $row->ongoing_date,
            $row->delivered_date,
            $row->completed_date,      
            $row->remitted_date,      
            $row->cancelled_date,
            $row->refunded_date,
        ];

        
        return $fields;
    }

    public function headings(): array
    {
        return [
            [
                'TRANSACTION ID',
                'REFERENCE NUMBER',
                'COMPANY NAME',
                'TOTAL AMOUNT',
                'Sales(Before VAT)',
                'TRANSACTION FEE',
                'SERVICE FEE',
                'COMMISSION FEE',
                'ONLINE PLATFORM VAT',
                'SHIPPING VAT',
                'ITEM VAT',
                'WITHHOLDING TAX(1%)',
                'TOTAL TAXES DUE',
                'STATUS',
                'PENDING DATE',
                'ONGOING DATE',
                'DELIVERY DATE',  
                'COMPLETED DATE',  
                'REMITTED DATE',  
                'CANCELLLED DATE',    
                'REFUNDED DATE',        
            ]            
        ];
    }
    
    public function startCell(): string
    {
        return 'A1';
    }

    public function columnWidths(): array
    {
        return [
            // 'B' => 10,
            // 'C' => 10,
            // 'D' => 10,
            // 'E' => 10,
            // 'F' => 10,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1 => ['font' => ['bold' => true , 'size' => 12 , 'name' => 'Calibri',]],
        ];
    }

    public function title(): string
    {
        return $this->title;
    }
}