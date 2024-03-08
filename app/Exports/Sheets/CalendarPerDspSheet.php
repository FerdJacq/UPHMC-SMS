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
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

use Log;
use DB;
use Auth;
use Helper;
use Illuminate\Support\Carbon;

class CalendarPerDspSheet implements FromCollection, WithEvents, WithMapping ,  WithHeadings,  ShouldAutoSize , WithStrictNullComparison , WithTitle , WithStyles , WithCustomStartCell , WithColumnWidths 
{
    protected int $counter = 1;
    protected $data;

    public function __construct($data,$title)
    {
        $this->data = $data;
        $this->title = $title;
    }
    
    public function collection()
    {   
        $data=$this->data["summaries"]->groupBy(function ($item, $key) {
            return $item->company_name;
        });
        $data = collect($data->mapWithKeys(function ($item,$key) {
            return  [
                $key =>[
                    'key'=>$key,
                    'company_name' =>$item->first()->company_name, 
                    'total_transactions' => $item->sum("total"),
                    'base_price' => $item->sum("base_price"),
                    'transaction_fee' => $item->sum('transaction_fee'),
                    'service_fee' => $item->sum('service_fee'),
                    'commission_fee' => $item->sum('commission_fee'),
                    'online_platform_vat' => $item->sum('online_platform_vat'),
                    'shipping_vat' => $item->sum('shipping_vat'),
                    'item_vat' => $item->sum('item_vat'),
                    'withholding_tax' => $item->sum('withholding_tax'),
                    'tax' => $item->sum('tax'),
                    // 'won' => $item->where('result', 'won')->count(),
                    // 'lost' => $item->where('result', 'lost')->count(),
                ]
            ];
        }))
        ->sortBy([['company_name', 'asc'],['region_code','asc'] ])
        ->values();
        return $data;
    }

    public function map($row): array
    {
        $fields = [          
            $row["company_name"],
            $row["total_transactions"],
            $row["base_price"],
            $row["online_platform_vat"],
            $row["service_fee"],
            $row["commission_fee"],
            $row["online_platform_vat"],
            $row["shipping_vat"],
            $row["item_vat"],
            $row["withholding_tax"],
            $row["tax"],
        ];
        return $fields;
    }

    public function headings(): array
    {
        return [
            [
                'DIGITAL SERVICE PROVIDER',
                'TOTAL TRANSACTION',
                'GROSS SALES',
                'TRANSACTION FEE',
                'SERVICE FEE',
                'COMMISSION FEE',
                'ONLINE PLATFORM VAT',
                'SHIPPING VAT',
                'ITEM VAT',
                'WITHHOLDING TAX(1%)',
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
            1 => ['font' => ['bold' => true , 'size' => 15 , 'name' => 'Calibri']],
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
                $first_column_index = 2;
                $first_column = Coordinate::stringFromColumnIndex($first_column_index);
                $first_row = 4;
                $last_row = $event->sheet->getHighestRow();
                $computation_row = $last_row+1;

                for($col=$first_column_index;$col<=$last_column_index;$col++){
                    $col_letter = Coordinate::stringFromColumnIndex($col);
                    $cell = Coordinate::stringFromColumnIndex($col) . $computation_row;
                    $event->sheet->getCell($cell)->setValue('=SUM('.$col_letter .$first_row.":" .$col_letter .$last_row. ')');
                }

                $fontStyle = [
                    'bold' => true,
                    'color' => ['rgb' => 'FF0000'], // Red font color
                    'italic' => true,
                    // 'size'  => 12,
                ];

                $event->sheet->getDelegate()
                ->getStyle($first_column . $computation_row . ':' . $last_column . $computation_row)
                ->getFont()
                ->applyFromArray($fontStyle);

                //apply number format
                $event->sheet->getDelegate()
                ->getStyle(Coordinate::stringFromColumnIndex($first_column_index+1) . $first_row . ':' . $last_column . $computation_row)
                ->getNumberFormat()
                ->setFormatCode('#,##0.00');

                $start_date =  Carbon::parse($this->data["start_date"])->format("M d, Y");
                $end_date =  Carbon::parse($this->data["end_date"])->format("M d, Y");
                $title = "From ".$start_date." to ".$end_date;
                $event->sheet->mergeCells('A1:C2');
                $event->sheet->getCell("A1")->setValue($title);

                if(!$this->data["seller_id"]) $event->sheet->getColumnDimension('C')->setVisible(false);

            },
        ];
    }
}