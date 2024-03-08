<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;


use App\Exports\Sheets\TransactionSheet;
use App\Exports\Sheets\SummaryTransactionSheet;
use App\Exports\Sheets\SellerInformationSheet;

class TransactionExport implements WithMultipleSheets
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
        if ($request->seller_id){
            $sheets[] = new SellerInformationSheet($request,"SELLER INFORMATION");
        }
        
        $sheets[] = new SummaryTransactionSheet($request,"SUMMARY");
        $sheets[] = new TransactionSheet($request,"DATA");

        return $sheets;
    }
}
