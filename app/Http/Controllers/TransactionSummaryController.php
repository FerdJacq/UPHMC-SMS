<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\ServiceProvider;
use App\Models\Transaction;
use App\Models\TransactionDetails;
use App\Models\TransactionSummary;
use DB;
use Log;

class TransactionSummaryController extends Controller
{
    //

    public static function update($new, $dsp, $transaction, $new_status, $current_status, $new_date)
    {

        $minus_current_status = [];
        $deductions = [];
        $increment_status = [];
        if ($new){
            $increment_status = [
                $new_status=>DB::raw($new_status."+1")
            ];
        }
        else if($new_status!=$current_status)
        {
            $increment_status = [
                $new_status=>DB::raw($new_status."+1")
            ];
        }
        if ($new_status=="completed")
            $deductions = [
                'transaction_fee'=>DB::raw("transaction_fee+".$transaction->transaction_fee),
                'service_fee'=>DB::raw("service_fee+".$transaction->service_fee),
                'commission_fee'=>DB::raw("commission_fee+".$transaction->commission_fee),
                'online_platform_vat'=>DB::raw("online_platform_vat+".$transaction->online_platform_vat),
                'shipping_vat'=>DB::raw("shipping_vat+".$transaction->shipping_vat),
                'item_vat'=>DB::raw("item_vat+".$transaction->item_vat),
                'base_price'=>DB::raw("base_price+".$transaction->base_price),
                'total_amount'=>DB::raw("total_amount+".$transaction->total_amount),
                'withholding_tax'=>DB::raw("withholding_tax+".$transaction->withholding_tax),
                'tax'=>DB::raw("tax+".$transaction->tax)
            ];

        if ($new_status=="refunded" && $current_status=="completed")
            $deductions = [
                'transaction_fee'=>DB::raw("transaction_fee-".$transaction->transaction_fee),
                'service_fee'=>DB::raw("service_fee-".$transaction->service_fee),
                'commission_fee'=>DB::raw("commission_fee-".$transaction->commission_fee),
                'online_platform_vat'=>DB::raw("online_platform_vat-".$transaction->online_platform_vat),
                'shipping_vat'=>DB::raw("shipping_vat-".$transaction->shipping_vat),
                'item_vat'=>DB::raw("item_vat-".$transaction->item_vat),
                'base_price'=>DB::raw("base_price-".$transaction->base_price),
                'total_amount'=>DB::raw("total_amount-".$transaction->total_amount),
                'withholding_tax'=>DB::raw("withholding_tax+".$transaction->withholding_tax),
                'tax'=>DB::raw("tax-".$transaction->tax)
            ];
        // Log::info("new_date".$new_date);
        // Log::info("old_date".$transaction[$current_status."_date"]);
        $new_date = Carbon::parse($new_date)->format('Y-m-d');
        $old_date = Carbon::parse($transaction[$current_status."_date"])->format('Y-m-d');
        if($new_date==$old_date && $new_status!=$current_status)
            $minus_current_status = [
                $current_status=>DB::raw("$current_status-1")
            ];

        $data = array_merge($increment_status,$deductions,$minus_current_status);
        $transaction_summary = TransactionSummary::updateOrCreate(
            [
                "seller_id"=>$transaction->seller_id,
                "service_provider_id"=>$dsp->id,
                "assigned_date"=>$new_date,
                "region_code"=>$transaction->region_code,
                "vat_type"=>$transaction->vat_type,
                "type"=>$transaction->type,
            ],$data
        );
    }

    //for backup only - 5/15/2023 - jondee
    public static function updateRaw($dsp, $transaction, $new_status, $current_status, $new_date)
    {

        $minus_current_status = $current_status."=".$current_status."-1";
        $add_new_status = $new_status."=".$new_status."+1";
        $transaction_fee = "transaction_fee=transaction_fee+".$transaction->transaction_fee;
        $service_fee = "service_fee=service_fee+".$transaction->service_fee;
        $commission_fee = "commission_fee=commission_fee+".$transaction->commission_fee;
        $tax = "tax=tax+".$transaction->tax;

        $deduction_query = ($new_status=="completed") ? ",$transaction_fee,$service_fee,$commission_fee,$tax" : "";

        if(in_array($new_date, [Carbon::parse($transaction->created_at)->toDateString(), $transaction[$current_status."_date"]]))
        {
            DB::update("UPDATE transaction_summaries set $minus_current_status,$add_new_status".$deduction_query
            ." where service_provider_id=? and assigned_date=?",[$dsp->id,$new_date]);
        }
        else
        {
            DB::update("UPDATE transaction_summaries set $add_new_status".$deduction_query
            ." where service_provider_id=? and assigned_date=?",[$dsp->id,$new_date]);
        }  
    }
}
