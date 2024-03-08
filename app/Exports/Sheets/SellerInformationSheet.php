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

use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

use Illuminate\Support\Carbon;

use Log;
use DB;
use Auth;
use Helper;

class SellerInformationSheet implements FromCollection ,  WithHeadings,  ShouldAutoSize , WithStrictNullComparison , WithTitle , WithStyles , WithCustomStartCell , WithColumnWidths,WithEvents, WithColumnFormatting 
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
        $seller = Seller::where("id",$request->seller_id)->first();


        return collect([
            ['SELLER INFORMATION'],
            ['REGISTERED NAME',$seller->registered_name],
            ['REGISTERED ADDRESS',$seller->registered_address],
            ['REGION',$seller->region->label],
            // ['BUSINESS NAME',$seller->business_name],
            ['TIN', (string)$seller->tin],
            ['EMAIL',$seller->email],
            ['CONTACT NUMBER',$seller->contact_number]
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
            // 'C' => 20
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
   
                $event->sheet->getDelegate()->getStyle('B2:F2')
                                ->getAlignment()
                                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);    
          
            },
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $date_from = Carbon::parse($this->request->start_date)->toFormattedDateString();
        $date_to = Carbon::parse($this->request->end_date)->toFormattedDateString();

        
        // $sheet->setCellValue('B1','From ' . $date_from . " - ". $date_to);   
        // $sheet->mergeCells('B1:C1'); 
        $sheet->mergeCells('B2:C2'); 
       
        return [
            // Style the first row as bold text.
            'B1'=>[
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],
            'B1'=>[
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],
            "C6" => ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT]],
            'B2:C2' => [
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
                'font' => ['bold' => true , 'size' => 12 , 'name' => 'Calibri', 'color' => $this->font_white],
                'fill' => ['startColor' => $this->primary_color, 'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID],
            ],
            'B2:C8' => [
                'borders' => [ 
                    'outline' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => $this->border_color,
                    ],
                ], 
            ],    
            // 'B10:C10' => [
            //     'fill' => ['startColor' => $this->warning_color, 'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID],
            // ],  
  
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
            // 'C8' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE,
        ];
    }
}