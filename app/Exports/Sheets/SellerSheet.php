<?php

namespace App\Exports\Sheets;

use App\Models\Transaction;
use App\Models\Seller;
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
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

use Log;
use DB;
use Auth;
use Helper;
use Carbon\Carbon;

class SellerSheet implements FromCollection, WithEvents, WithMapping,  WithHeadings,  ShouldAutoSize , WithStrictNullComparison , WithTitle , WithStyles , WithCustomStartCell , WithColumnWidths 
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
        $date_type = ($request->selected_date_type=="PENDING") ? "created_at" : strtolower($request->selected_date_type."_date");
        $start_date = $request->start_date ? date('Y-m-d', strtotime($request->start_date)) : date('Y-m-d');
        $end_date =  $request->end_date ? date('Y-m-d', strtotime($request->end_date)) :  date('Y-m-d');
        $eligible_witheld_seller = ($request->eligible_witheld_seller) ? $request->eligible_witheld_seller : [];

        $data = Seller::with(["serviceProvider"])
        ->leftJoin('transactions as t', function($join) use ($start_date,$end_date,$request)
        {
            $join->on('t.seller_id', '=', 'sellers.id')
            ->when(Helper::auth_role_dsp(),function($query){
                $query->whereIn("t.service_provider_id",Helper::auth_dsp_list("service_provider_id"));
            })
            // ->when(Helper::auth_role_rdo(),function($query){
            //     $query->whereIn("t.region_code",Helper::auth_region_list());
            // })
            ->where("t.status","COMPLETED")
            ->when(in_array($request->selected_type,[1,2,3]),function($q) use ($start_date,$end_date,$request){
                if($request->selected_type==1)
                    $q->whereRaw("date(t.remitted_date) between ? and ?",[$start_date,$end_date]);
                else
                    $q->whereRaw("date(t.completed_date) between ? and ?",[$start_date,$end_date]);
            });
        })
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
        ->when(in_array($request->selected_type,[1,2]),function($q) use ($request){
            if($request->selected_type==1)
                $q->whereNotNull("remitted_date");
            else
                $q->whereNull("remitted_date");
        })
        // ->where("t.status","COMPLETED")
        // ->whereRaw("date(t.completed_date) between ? and ?",[$start_date,$end_date])
        ->when(Helper::auth_role_rdo(),function($query){
            $query->whereIn("sellers.region_code",Helper::auth_region_list());
        })
        ->groupBy("sellers.id","registered_name", "registered_address", "business_name", "tin", "sellers.vat_type", "sellers.contact_number", "sellers.email")
        ->select(
            'sellers.id',
            'sellers.registered_name',
            'sellers.registered_address',
            'sellers.business_name',
            'sellers.tin',
            'sellers.vat_type',
            'sellers.contact_number',
            'sellers.email',
            'sellers.eligible_witheld_seller',
            'sellers.sales_per_anum',
            'sellers.type',
            'sellers.has_cor',
            'sellers.created_at',
            'sellers.updated_at',
            DB::raw('SUM(t.online_platform_vat) as online_platform_vat'),
            DB::raw('SUM(t.shipping_vat) as shipping_vat'),
            DB::raw('SUM(t.item_vat) as item_vat'),
            DB::raw('SUM(t.base_price) as base_price'),
            DB::raw('SUM(t.tax) as tax'),
            DB::raw('SUM(t.withholding_tax) as withholding_tax'),
        )
        // ->selectRaw('count(sellers.id) as id,sellers.registered_name, registered_address,sellers.business_name,sellers.tin,sellers.vat_type,sellers.contact_number,sellers.email,sellers.created_at,sellers.updated_at,
        // sum(t.online_platform_vat) as online_platform_vat,sum(t.shipping_vat) as shipping_vat,sum(t.item_vat) as item_vat,sum(t.base_price) as base_price,sum(t.tax) as tax,sum(t.withholding_tax) as withholding_tax')
        ->orderBy("sellers.business_name")
        ->get();

        return $data;
    }

    public function map($row): array
    {
        $fields = [          
            $row->registered_name,
            $row->registered_address,
            // $row->business_name,
            $row->tin,
            // ($row->vat_type=="V") ? "VAT" : "NON-VAT",
            $row->contact_number,
            $row->email,
            // ($row->item_vat + $row->online_platform_vat + $row->shipping_vat),
            $row->base_price,
            $row->withholding_tax,
            $row->tax
        ];

        
        return $fields;
    }

    public function headings(): array
    {
        return [
            [
                'BUSINESS NAME',
                'ADDRESS',
                // 'COMPANY NAME',
                'TIN',
                // 'VAT TYPE',
                'CONTACT NUMBER',
                'EMAIL',
                'GROSS SALES',
                'WITHOLDING TAX(1%)',
                'TOTAL TAXES DUE'  
            ]            
        ];
    }
    
    public function startCell(): string
    {
        return 'A3';
    }

    public function columnWidths(): array
    {
        return [
            'B' => 10,
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
            3 => ['font' => ['bold' => true , 'size' => 12 , 'name' => 'Calibri']],
            "C" => ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT]],
        ];
    }

    public function title(): string
    {
        return $this->title;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $last_column = $event->sheet->getHighestColumn();
                $last_column_index = Coordinate::columnIndexFromString($event->sheet->getHighestColumn());
                $first_column_index = 6;
                $first_column = Coordinate::stringFromColumnIndex($first_column_index);
                $first_row = 4;
                $last_row = $event->sheet->getHighestRow();
                // $computation_row = $last_row+1;
                $computation_row = 2;

                for($col=$first_column_index;$col<=$last_column_index;$col++){
                    $col_letter = Coordinate::stringFromColumnIndex($col);
                    $cell = Coordinate::stringFromColumnIndex($col) . $computation_row;
                    $event->sheet->getCell($cell)->setValue('=SUM('.$col_letter .$first_row.":" .$col_letter .$last_row. ')');
                }

                $fontStyle = [
                    'bold' => true,
                    'color' => ['rgb' => 'FF0000'], // Red font color
                    'italic' => true,
                    'format'=>"#,##0.00"
                    // 'size'  => 12,
                ];

                $event->sheet->getDelegate()
                ->getStyle($first_column . $computation_row . ':' . $last_column . $computation_row)
                ->getFont()
                ->applyFromArray($fontStyle);

                //apply number format
                $event->sheet->getDelegate()
                ->getStyle(Coordinate::stringFromColumnIndex($first_column_index) . $first_row . ':' . $last_column . $last_row)
                ->getNumberFormat()
                ->setFormatCode('#,##0.00');

                $event->sheet->getDelegate()
                ->getStyle(Coordinate::stringFromColumnIndex($first_column_index) . $computation_row . ':' . $last_column . $computation_row)
                ->getNumberFormat()
                ->setFormatCode('#,##0.00');

                $request = $this->request;
                $start_date =  Carbon::parse($request->start_date)->format("M d, Y");
                $end_date =  Carbon::parse($request->end_date)->format("M d, Y");
                $title = "From ".$start_date." to ".$end_date;
                $event->sheet->mergeCells('A1:C2');
                $event->sheet->getCell("A1")->setValue($title);

            },
        ];
    }
}