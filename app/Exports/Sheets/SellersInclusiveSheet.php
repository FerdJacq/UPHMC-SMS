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

class SellersInclusiveSheet implements FromCollection, WithEvents, WithMapping,  WithHeadings,  ShouldAutoSize , WithStrictNullComparison , WithTitle , WithStyles , WithCustomStartCell , WithColumnWidths 
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
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        
        $query = DB::table('sellers as s')
        ->leftjoin('transactions as t', 't.seller_id', '=', 's.id')
        ->whereRaw("date(completed_date) between ? and ?",[$start_date,$end_date])
        ->when(Helper::auth_role_dsp(),function($q){
            $q->whereIn("t.service_provider_id",Helper::auth_dsp_list("service_provider_id"));
        })
        ->when(in_array($request->type,[1,2]),function($q) use ($request){
            if($request->type==1)
                $q->whereNotNull("remitted_date");
            else
                $q->whereNull("remitted_date");
        })
        ->when($request->eligible_witheld_seller, function ($query, $value) {
            $query->whereIn("eligible_witheld_seller",$value);
        })
        ->when($request->has_cor=="WCOR", function ($query, $value) {
            $query->where("has_cor",1);
        })
        ->when($request->has_cor=="WOCOR", function ($query, $value) {
            $query->where("has_cor",0);
        })
        // ->where("t.status","COMPLETED")
        ->groupBy("registered_name", "registered_address", "business_name", "tin", "s.vat_type", "s.contact_number", "s.email")
        ->selectRaw('s.registered_name, registered_address,s.business_name,s.tin,s.vat_type,s.contact_number,s.email,
        sum(t.online_platform_vat) as online_platform_vat,sum(t.shipping_vat) as shipping_vat,sum(t.item_vat) as item_vat,sum(t.base_price) as base_price,sum(t.tax) as tax,sum(t.withholding_tax) as withholding_tax')
        ->get();

        return $query;
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
            3 => ['font' => ['bold' => true , 'size' => 12 , 'name' => 'Calibri']],
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
                $first_column_index = 8;
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