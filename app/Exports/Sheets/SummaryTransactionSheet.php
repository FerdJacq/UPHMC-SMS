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

use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

use Illuminate\Support\Carbon;

use Log;
use DB;
use Auth;
use Helper;

class SummaryTransactionSheet implements FromCollection ,  WithHeadings,  ShouldAutoSize , WithStrictNullComparison , WithTitle , WithStyles , WithCustomStartCell , WithColumnWidths,WithEvents, WithColumnFormatting 
{
    protected int $counter = 1;
    protected $request;
    protected $title;
    protected $border_color = array('rgb' => '000000');
    protected $primary_color = array('rgb' => '708090');
    protected $warning_color = array('rgb' => 'FFEFD5');
    protected $gray_color = array('rgb' => 'DCDCDC');
    protected $font_white =  array('rgb' => 'FFFFFF');

    public function __construct($request,$title)
    {
        $this->request = $request;
        $this->title = $title;
    }
    
    public function collection()
    {      

        $request = $this->request;
    
        $date_type = strtolower($request->selected_date_type);
        $start_date =  date('Y-m-d', strtotime($request->start_date));
        $end_date =  date('Y-m-d', strtotime($request->end_date));
        $seller = DB::table("sellers")->where("tin","029203920132")->first();

        $summary=DB::table('transactions as t')
        ->join('service_providers as sp', 't.service_provider_id', '=', 'sp.id')
        ->when($request->seller_id,function($query) use($request){
            $query->where("seller_id",$request->seller_id);
        })
        ->when($request->selected_date_type, function ($query, $value) use($date_type,$start_date,$end_date) {
            $query->whereBetween(DB::raw('DATE(t.'.$date_type.')'), [$start_date, $end_date]);
        })
        ->when(Helper::auth_role_dsp(),function($query){
            $query->whereIn("t.service_provider_id",Helper::auth_dsp_list("service_provider_id"));
        })
         ->when(Helper::auth_role_seller(),function($query) use($seller){
             $seller_id = ($seller) ? $seller->id : 0;
           $query->whereIn("service_provider_id",Helper::auth_dsp_list("service_provider_id"))
            ->where("seller_id",$seller_id);
        })
        ->when(Helper::auth_role_rdo(),function($query) use($seller){
            $query->whereIn("t.region_code",Helper::auth_region_list());
        })
        ->when($request->selected_regions,function($query) use($request){
            $query->whereIn("t.region_code",$request->selected_regions);
        })
        ->when($request->transaction_status,function($query) use($request){
            $query->whereIn("t.status",$request->transaction_status);
        })
        ->when(in_array($request->selected_type,[1,2]),function($q) use ($request){
            if($request->selected_type==1)
                $q->whereNotNull("remitted_date");
            else
                $q->whereNull("remitted_date");
        })
        ->when($request->selected_dsp,function($query) use($request){
            $query->whereIn("t.service_provider_id",$request->selected_dsp);
        })
        ->selectRaw('
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
        ->first();

        $total_transactions = $summary->pending + $summary->ongoing + $summary->delivered + $summary->completed +  $summary->cancelled +  $summary->refunded;
        return collect([
            ['COMPLETED TRANSACTION'],
            ['Transaction Fee',$summary->transaction_fee],
            ['Service Fee',$summary->service_fee],
            ['Commission Fee',$summary->commission_fee],
            ['Online Platform VAT',$summary->online_platform_vat],
            ['Shipping VAT',$summary->shipping_vat],
            ['Item VAT',$summary->item_vat],
            ['Withholding Tax(1%)',$summary->withholding_tax],
            ['Total Taxes Due',$summary->tax],
            [''],
            ['Total Amount',$summary->total_amount],
            ['Sales(Before VAT)',$summary->base_price],
            [''],
            ['Pending',$summary->pending],
            ['Ongoing',$summary->ongoing],
            ['Delivered',$summary->delivered],
            ['Completed',$summary->completed],
            ['Cancelled',$summary->cancelled],
            ['Refunded',$summary->refunded],
            ['Total Transactions',$total_transactions],
        ]);
    }


    public function headings(): array
    {
        return [           
        ];
    }
    
    public function startCell(): string
    {
        return 'B2';
    }

    public function columnWidths(): array
    {
       
        return [
            'B' => 20,
            'C' => 20
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
   
                $event->sheet->getDelegate()->getStyle('B2:F2')
                                ->getAlignment()
                                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);    
                                
            $event->sheet->getDelegate()
            ->getStyle('C1:C13')
            ->getNumberFormat()
            ->setFormatCode('#,##0.00');

            $event->sheet->getDelegate()
            ->getStyle('C14:C100')
            ->getNumberFormat()
            ->setFormatCode('#,##0');
            },
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $date_from = Carbon::parse($this->request->start_date)->toFormattedDateString();
        $date_to = Carbon::parse($this->request->end_date)->toFormattedDateString();

        
        $sheet->setCellValue('B1','From ' . $date_from . " - ". $date_to);   
        $sheet->mergeCells('B1:C1'); 
        $sheet->mergeCells('B2:C2'); 
       
        return [
            // Style the first row as bold text.
            'B1'=>[
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],
            'B2:C2' => [
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
                'font' => ['bold' => true , 'size' => 12 , 'name' => 'Calibri', 'color' => $this->font_white],
                'fill' => ['startColor' => $this->primary_color, 'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID],
            ],
            'B2:C10' => [
                'borders' => [ 
                    'outline' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => $this->border_color,
                    ],
                ], 
            ],    
            'B10:C10' => [
                'fill' => ['startColor' => $this->warning_color, 'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID],
            ],  

            'B12:C13' => [
                'borders' => [ 
                    'outline' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => $this->border_color,
                    ],
                ], 
                'fill' => ['startColor' => $this->gray_color, 'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID],
            ],    

            'B15:C21' => [
                'borders' => [ 
                    'outline' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => $this->border_color,
                    ],
                ], 
            ],   
            'B21:C21' => [
                'fill' => ['startColor' => $this->gray_color, 'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID],
            ],     
            'B' => ['font' => ['bold' => true , 'size' => 11 , 'name' => 'Calibri']],
        ];
    
    }

    public function title(): string
    {
        return $this->title;
    }

    public function columnFormats(): array
    {
        return [
            'C8' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE,
        ];
    }
}