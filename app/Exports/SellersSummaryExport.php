<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

use Helper;
use DB;

use App\Exports\Sheets\SellersInclusiveSheet;
use App\Exports\Sheets\SellerSheet;



class SellersSummaryExport implements WithMultipleSheets
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
        $request->start_date = date('Y-m-d', strtotime($request->start_date));
        $request->end_date = date('Y-m-d', strtotime($request->end_date));
        $type = "All";
        if ($request->type==1)
            $type="Remitted Only";
        else if ($request->type==2)
            $type="Not Remitted Only";

        $sheets[] = new SellerSheet($request,"Data($type)");
      
        return $sheets;
    }
}