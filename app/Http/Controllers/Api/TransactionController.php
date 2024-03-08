<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Carbon;

use App\Http\Controllers\TransactionSummaryController;
use App\Http\Controllers\EmailNotificationController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceProvider;
use App\Models\Transaction;
use App\Models\TransactionDetails;
use App\Models\TransactionCustomers;
use App\Models\TransactionSummary;

use App\Models\Customer;
use App\Models\Seller;

use App\Models\Region;
use App\Models\SellerServiceProvider;

use App\Http\Controllers\FileController;

use Validator;
use DB;
use Log;
use Helper;

use Illuminate\Support\Facades\Artisan;


class TransactionController extends Controller
{
    /*

    transaction fee = (total price - (voucher + coins)) * 0.02
    commission fee = (total price - (voucher + coins)) * 0.01
    service fee = (total price - (voucher + coins)) * 0.03 //cap 15php equivalent

    online platform vat = (transaction fee + commission fee + service fee) * 0.12

    shipping vat = shipping fee * 0.12

    item vat = (subtotal amount - (transaction fee + commission fee + service fee) - voucher) * 0.12

    tax = online platform vat + shipping vat + item vat

    */
    public function save(Request $request)
    {
        $code = $request->header("dsp-code");
        $secret = $request->header("dsp-secret");
        $token = $request->header("dsp-token");
        $current_date = date('Y-m-d');

        $request->customer = [
            "first_name"=>"Marilou",
            "middle_name"=>"",
            "last_name"=>"Ballesteros",
            "birth_date"=> isset($request->customer["birth_date"]) ? $request->customer["birth_date"] : null,
            "mobile_number"=> isset($request->customer["mobile_number"]) ? $request->customer["mobile_number"] : "09382917291",
            "email"=> isset($request->customer["email"]) ? $request->customer["email"] : ""
        ];

        $validator = Validator::make($request->all(), [
                "trans_id" => "required|string",
                "shipping_fee" => "required|numeric",
                "coins" => "required|numeric",
                "subtotal_amount" => "required|numeric",
                "total_amount" => "required|numeric",
                "items"=>"required|array|min:1",
                "status"=>"in:PENDING,ONGOING,DELIVERED,COMPLETED,CANCELLED",
                "seller.tin"=>"required",
                "seller.registered_name"=>"required",
                "seller.vat_type"=>"required|in:V,NV",
                // 'PENDING', 'ONGOING', 'DELIVERED', 'CANCELLED', 'REFUNDED', 'COMPLETED'
        ]);
        
        if ($validator->fails()) {
            $errorString = str_replace("."," ",implode(", ",$validator->messages()->all()));
            return response()->json(["status"=>0,"message"=>"MISSING_FIELD","data"=>trim($errorString)], 400);
        }

        $status = $request->status;
        if (!$status) $status = "PENDING";
        
        $dsp = ServiceProvider::with(["fees"=> function($q){
            $q->where("status","ACTIVE");
        }])->where("code",$code)
        ->where("secret",$secret)
        ->where("token",$token)
        ->whereHas('fees', function ($query) {
            $query->where('status','=',"ACTIVE");            
        })
        ->where("status","ACTIVE")
        ->first();

        $region_code = ($request->region_code) ? $request->region_code : "01";

        if ($region_code){
            $region_code = Region::where("region_code",$region_code)->value('region_code');
            if(!$region_code)
                return response()->json(["status"=>0,"message"=>"INVALID_REGION"], 400);
        }

        if (!$dsp)
            return response()->json(["status"=>0,"message"=>"ACCESS_DENIED"], 401);

        try {
            //get transaction if exist;
            $transaction = Transaction::where("service_provider_id",$dsp->id)
            ->where("trans_id",$request->trans_id)
            ->first();
            
            //check if the transaction is not pending
            if ($transaction && $transaction->status!="PENDING")
                return response()->json(["status"=>0,"message"=>"ACTION_DENIED"], 403);

            $items = collect($request->items)->map(function ($item) {
                $item['item'] = $item['description'];
                return $item;
            });

            //check total of items vs subtotal
            $total = collect($items)->sum("total_price");
            if ($total!=$request->subtotal_amount)
                return response()->json(["status"=>0,"message"=>"ITEM_DISCREPANCY"], 400);

            $additional_fees = $request->shipping_fee;
            $discounts = $request->voucher + $request->coins;
            $net = ($total + $additional_fees) - $discounts;
            $subtotal = ($total  - $discounts);

            //check net total vs computation from client
            if ($net!=$request->total_amount)
                return response()->json(["status"=>0,"message"=>"AMOUNT_DISCREPANCY"], 400);


            $fee_type = ["TRANSACTION","SERVICE","COMMISSION"];
            $fee_list = $dsp->fees; //array of collection

            $computed_fee = [];

            foreach ($fee_type as $type) {
                $amount = 0;
                $fee = $fee_list->where("type",$type)->where("min","<=",$net)->where("max",">=",$net)->first();
                if (!$fee)
                    $fee = $fee_list->where("type",$type)->where("min",0)->where("max",0)->first();

                if ($fee)
                {
                    if($type=="COMMISSION" && $fee->amount_type=="PERCENTAGE")
                    {
                        $percentage_fee = $fee->amount / 100;
                        $amount = $subtotal * $percentage_fee;
                    }
                    else if($fee->amount_type=="PERCENTAGE")
                    {
                        $percentage_fee = $fee->amount / 100;
                        $amount = $net * $percentage_fee;
                    }
                    else if($fee->amount_type=="FIXED")
                    {
                        $amount = $fee->amount;
                    }
                }

                array_push($computed_fee, $amount);
            }

            $online_platform_vat = (array_sum($computed_fee) / 1.12) * 0.12;
            $shipping_vat = ($request->shipping_fee / 1.12) * 0.12;
            $product_less_discount = $request->subtotal_amount - $discounts;
            if ($request->seller["vat_type"]=="NV"){
                $base_price = $product_less_discount;
                $item_vat = 0;
            }else{
                $base_price = ($product_less_discount / 1.12);
                $item_vat = $base_price * 0.12;
            }
            
            $withholding_tax = $base_price * 0.005;
            $tax = $online_platform_vat + $shipping_vat + $item_vat + $withholding_tax;

            return DB::transaction(function () 
            use($transaction, $items, $dsp, $request, $computed_fee,$online_platform_vat,$shipping_vat,$item_vat,$base_price,$withholding_tax, $tax, $status, $current_date,$region_code) {
                $new = true;
                $old_status = $status;
                if ($transaction){
                    $new = false;
                    $old_status = $transaction->status;
                }
                $customer = $this->createCustomer($dsp,$new,$transaction,$request);
                $seller = $this->createSeller($dsp,$new,$transaction,$request,$base_price,$status);

                if ($new){
                    $reference_number = Helper::ref_number($dsp->prefix."0",32);
                    $transaction = new Transaction();
                    // $transaction->or_number = Helper::ref_number($dsp->prefix."0",15);
                    $transaction->service_provider_id = $dsp->id;
                    $transaction->trans_id = $request->trans_id;
                    $transaction->reference_number = $reference_number;
                }
                $transaction->customer_id = ($customer) ? $customer->id : null;
                $transaction->seller_id = ($seller) ? $seller->id : null;
                $transaction->shipping_fee = $request->shipping_fee;
                $transaction->voucher = $request->voucher;
                $transaction->coins = $request->coins;
                $transaction->subtotal_amount = $request->subtotal_amount;
                $transaction->total_amount = $request->total_amount;
                $transaction->transaction_fee = $computed_fee[0];
                $transaction->service_fee = $computed_fee[1];
                $transaction->commission_fee = $computed_fee[2];
                $transaction->online_platform_vat = $online_platform_vat;
                $transaction->shipping_vat = $shipping_vat;
                $transaction->item_vat = $item_vat;
                $transaction->base_price = $base_price;
                $transaction->vat_type = $request->seller["vat_type"];
                $transaction->withholding_tax = $withholding_tax;
                $transaction->tax = $tax;
                $transaction->status = $status;
                $transaction->region_code = $region_code;
                $transaction->type = ($request->type) ? $request->type : "PRODUCT";
                // $transaction->email_notified = 2;
                $transaction->remitted_date = $request->remitted_date  ? $request->remitted_date : null;
                $transaction[strtolower($status)."_date"] = ($request->status_date!="") ? $request->status_date : Carbon::now();
                $transaction->save();

                if ($new)
                {
                    //for setting or_number
                    DB::Statement("update
                    transactions as t inner join series as s on t.service_provider_id=s.service_provider_id
                    set t.or_number=s.complete_no,s.status=0
                    where s.status=1 and t.id=$transaction->id limit 1;");

                    TransactionSummaryController::update($new, $dsp, $transaction, strtolower($status), strtolower($status), $transaction[strtolower($status)."_date"]);
                }
                else
                {
                    TransactionSummaryController::update($new, $dsp, $transaction, strtolower($status), strtolower($old_status), $transaction[strtolower($status)."_date"]);
                }
                
                $transaction->details()->delete();
                $transaction->details()->createMany($items);

                //2023-06-14 - remove by jondee rigor
                // if($status=="COMPLETED" && isset($request->customer))
                // {
                //     if($request->customer["email"])
                //         $notify = EmailNotificationController::officialReceipt($transaction->reference_number);
                // }

                foreach ($items as $key => $value) {
                    $image=isset($value["image"]) ? $value["image"] : "";
                    $item = $transaction->details[$key];
                    if($image){
                        FileController::fileUpload($item->id,$item->id,"transaction_details",$image);
                    }
                }

                $data = [
                    "reference_number"=>$transaction->reference_number,
                    "transaction_fee"=>limitDecimal($transaction->transaction_fee,2),
                    "service_fee"=>limitDecimal($transaction->service_fee,2),
                    "commission_fee"=>limitDecimal($transaction->commission_fee,2),
                    "online_platform_vat"=>limitDecimal($transaction->online_platform_vat,2),
                    "shipping_vat"=>limitDecimal($transaction->shipping_vat,2),
                    "item_vat"=>limitDecimal($transaction->item_vat,2),
                    "base_price"=>limitDecimal($transaction->base_price,2),
                    "withholding_tax"=>limitDecimal($transaction->withholding_tax,2),
                    "tax"=>limitDecimal($transaction->tax,2)
                ];

                $test = Helper::useRedis("new_transaction",$data);

                return response()->json(["status"=>1,"data"=>$data], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);
            },20);
        
        } catch (Throwable $e) {
            throw $e;
            return false;
        }
    }

    public function update(Request $request)
    {
        $code = $request->header("dsp-code");
        $secret = $request->header("dsp-secret");
        $token = $request->header("dsp-token");

        $validator = Validator::make($request->all(), [
                "reference_number" => "required|string",
                "status" => "required|string"
        ]);
        
        if ($validator->fails()) {
            $errorString = str_replace(".","",implode(", ",$validator->messages()->all()));
            return response()->json(["status"=>0,"message"=>"MISSING_FIELD"], 400);
        }
        
        $dsp = ServiceProvider::where("code",$code)
        ->where("secret",$secret)
        ->where("token",$token)
        ->where("status","ACTIVE")
        ->first();

        if (!$dsp)
            return response()->json(["status"=>0,"message"=>"ACCESS_DENIED"], 401);

        try {
            //get transaction if exist;
            $transaction = Transaction::where("reference_number",$request->reference_number)
            ->where("service_provider_id",$dsp->id)
            ->first();

            //for testing only
            // $transaction->status="PENDING";
            // $transaction->save();

            if (!$transaction)
                return response()->json(["status"=>0,"message"=>"INVALID"], 500);

            $allowed_status = [
                "PENDING"=>["ONGOING","CANCELLED"],
                "ONGOING"=>["DELIVERED","CANCELLED","COMPLETED"],
                "DELIVERED"=>["CANCELLED","COMPLETED"],
                "COMPLETED"=>["CANCELLED","REFUNDED"],
                "CANCELLED"=>["REFUNDED"]
            ];

            $current_status = $transaction->status;
            $new_status = strtoupper($request->status);
            $arr_allowed_status = $allowed_status[$transaction->status] ?? null;
            $current_date = date('Y-m-d');

            if ($arr_allowed_status && in_array($new_status, $arr_allowed_status))
            {
                return DB::transaction(function () use($dsp, $transaction, $new_status, $current_status, $current_date) {
                    $current_status = strtolower($current_status);
                    $new_status = strtolower($new_status);

                    $transaction->status=strtoupper($new_status);
                    $transaction[$new_status."_date"] = Carbon::now();
                    $transaction->save();

                    TransactionSummaryController::update(false,$dsp, $transaction, $new_status, $current_status, $current_date);
                    
                    $data = ["reference_number"=>$transaction->reference_number, "status"=>strtoupper($new_status)];
                    return response()->json(["status"=>1,"data"=>$data], 200);
                });

                
            }
            else
            {
                // $message= "Status ".$status." is not allowed, current status is ". $transaction->status;
                if ($arr_allowed_status)
                {
                    $allowed = implode(",",$arr_allowed_status);
                    $are = count($arr_allowed_status) >1 ? "are" : "is";
                    $message = "ACTION_DENIED";
                }
                else
                {
                    $message = "ACTION_DENIED";
                }
              
                return response()->json(["status"=>0,"message"=>$message], 403);
            }
        
        } catch (Throwable $e) {
            throw $e;
        }
    }

    public function verify($search)
    {
        $search = str_replace("#","",$search);
        $data = Transaction::with(["details","ServiceProvider"])
        ->where("trans_id",$search)
        ->orWhere("reference_number",$search)
        ->orWhere("or_number",$search)
        ->first();
        // /49e430bf-8bde-41aa-9d88-d5dd1892f564?time=24hours&slide=04e92b9b-2d01-447a-8b4d-ad9ac4ce9d28
        if ($data)
            $data->blockchain_url = env("KALEIDO_FIREFLY_EXPLORER")."/".$data->blockchain_trx_id."?time=24hours&slide=".$data->blockchain_block_number;

        return response()->json(["status"=>1,"data"=>$data], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    private function createCustomer($dsp,$new,$transaction,$request){
        $customer = null;
        if (isset($request->customer))
        {
            if ($new){
                $customer = Customer::firstOrNew(
                    [
                        'service_provider_id' => $dsp->id,
                        'first_name' => isset($request->customer["first_name"]) ? $request->customer["first_name"] : "",
                        'middle_name' => isset($request->customer["middle_name"]) ? $request->customer["middle_name"] : "",
                        'last_name' => isset($request->customer["last_name"]) ? $request->customer["last_name"] : "",
                        'birth_date' => isset($request->customer["birth_date"]) ? $request->customer["birth_date"] : null
                    ]
                );
            }
            else
            {
                $customer = Customer::firstOrNew(
                    [
                        'id' => $transaction->customer_id
                    ]
                );
                $customer->first_name = isset($request->customer["first_name"]) ? $request->customer["first_name"] : "";
                $customer->middle_name = isset($request->customer["middle_name"]) ? $request->customer["middle_name"] : "";
                $customer->last_name = isset($request->customer["last_name"]) ? $request->customer["last_name"] : "";
                $customer->birth_date = isset($request->customer["birth_date"]) ? $request->customer["birth_date"] : null;
            }
            $customer->mobile_number = isset($request->customer["mobile_number"]) ? $request->customer["mobile_number"] : "";
            $customer->email = isset($request->customer["email"]) ? $request->customer["email"] : "";
            $customer->save();
        }

        return $customer;
    }

    public function set_remitted(Request $request){

        $code = $request->header("dsp-code");
        $secret = $request->header("dsp-secret");
        $token = $request->header("dsp-token");

        $validator = Validator::make($request->all(), [
            "trans_ids"  => "required|array|min:1",
            "trans_ids.*"  => "required|string|distinct|min:1",
            "remitted_date"=>"required|date_format:Y-m-d H:i:s"
        ]);

        if ($validator->fails()) {
            $errorString = str_replace("."," ",implode(", ",$validator->messages()->all()));
            return response()->json(["status"=>0,"message"=>"MISSING_FIELD","data"=>trim($errorString)], 400);
        }

        $dsp = ServiceProvider::where("code",$code)
        ->where("secret",$secret)
        ->where("token",$token)
        ->where("status","ACTIVE")
        ->first();

        if (!$dsp)
            return response()->json(["status"=>0,"message"=>"ACCESS_DENIED"], 401);

        //for the purpose of response the affected trans_ids
        $trans_ids = Transaction::whereIn("trans_id",$request->trans_ids)
        // ->where("service_provider_id",$dsp->id) //remove this for testing purpose
        ->where("status","COMPLETED")
        ->pluck('trans_id');

        //bulk update 
        $data = Transaction::whereIn("trans_id",$trans_ids)
        // ->where("service_provider_id",$dsp->id) //remove this for testing purpose
        ->where("status","COMPLETED")
        ->update(["remitted_date"=>$request->remitted_date]);

        return response()->json(["status"=>1,"data"=>$trans_ids], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    private function createSeller($dsp,$new,$transaction,$request,$base_price,$status){
        $seller = null;
        $region_code = ($request->region_code) ? $request->region_code : "01";
        if (isset($request->seller))
        {
            try
            {
            
                if ($new){
                    $seller = Seller::firstOrNew(
                        [
                            'registered_name' => isset($request->seller["registered_name"]) ? $request->seller["registered_name"] : "",
                            'tin' => $request->seller["tin"],
                            "region_code"=>$region_code
                        ]
                    );
                }
                else
                {
                    
                    $seller = Seller::firstOrNew(
                        [
                            'id' => $transaction->seller_id
                        ]
                    );
                }

                $seller->registered_address = isset($request->seller["registered_address"]) ? $request->seller["registered_address"] : "";
                $seller->business_name = isset($request->seller["business_name"]) ? $request->seller["business_name"] : "";
                // $seller->tin = isset($request->seller["tin"]) ? $request->seller["tin"] : "";
                $seller->email = isset($request->seller["email"]) ? $request->seller["email"] : "";
                $seller->vat_type = $request->seller["vat_type"];
                $seller->type = $request->seller["seller_type"] ?? "INDIVIDUAL";
                // $seller->eligible_witheld_seller = $request->seller["eligible_witheld_seller"];
                $seller->contact_number = $request->seller["contact_number"];
                $seller->save();

            } catch (\Exception $e) {
                $seller = Seller::where("tin",$request->seller["tin"])->lockForUpdate()->first();
                if (!$seller){
                    $seller = New Seller();
                    $seller->tin = $request->seller["tin"];
                }
                $seller->registered_name = isset($request->seller["registered_name"]) ? $request->seller["registered_name"] : "";
                $seller->registered_address = isset($request->seller["registered_address"]) ? $request->seller["registered_address"] : "";
                $seller->business_name = isset($request->seller["business_name"]) ? $request->seller["business_name"] : "";
                // $seller->tin = isset($request->seller["tin"]) ? $request->seller["tin"] : "";
                $seller->email = isset($request->seller["email"]) ? $request->seller["email"] : "";
                $seller->vat_type = $request->seller["vat_type"];
                $seller->type = $request->seller["seller_type"] ?? "INDIVIDUAL";
                // $seller->eligible_witheld_seller = $request->seller["eligible_witheld_seller"];
                $seller->contact_number = $request->seller["contact_number"];
                $seller->save();
            }

            //for demo only -2024-18-01 -jondee
            if ($status=="COMPLETED")
            {
                if($seller->tin!="029203920132")
                {
                    $cor = rand(0,1) == 1;
                    $seller->update(["sales_per_anum"=>DB::raw("sales_per_anum+".$base_price),"has_cor"=>$cor]);
                }
                else
                {
                    $seller->update(["sales_per_anum"=>DB::raw("sales_per_anum+".$base_price)]);
                }
               
                $seller = $seller->fresh();
    
                if(floatval($seller->sales_per_anum)>=500000 && $seller->eligible_witheld_seller=="NONE"){
                    if($seller->tin!="029203920132")
                    {
                        $status = collect(['ELIGIBLE', 'ACTIVE'])->random();
                        if ($status=="ACTIVE") $seller->update(["eligible_witheld_seller"=>$status,"has_cor"=>1]);
                        else $seller->update(["eligible_witheld_seller"=>$status]);
                        
                    }
                    else
                    {
                        $seller->update(["eligible_witheld_seller"=>"ELIGIBLE"]);
                    }
                
                }
            }
            //for demo only -2024-18-01 -jondee

            $user = SellerServiceProvider::firstOrCreate(
                [
                    'seller_id' =>  $seller->id,
                    'service_provider_id' => $dsp->id
                ]
            );
        }

        return $seller;
    }

    public function qr_set_status(Request $request){
        
        $current_date = date('Y-m-d');
        $data="";
        $seller_id = DB::table("sellers")->where("tin","029203920132")->pluck('id')->first();
        if ($request->completed){
            $data = Transaction::where("status","DELIVERED")
            ->where("seller_id",$seller_id)
            ->orderBy('id', 'desc')->first();
            $new_status = "COMPLETED";
            $old_status = "DELIVERED";
            if ($data){
                $this->set_latest_status($data,$new_status,$old_status,$current_date);
                // TransactionSummaryController::update(false,$dsp, $data, strtolower($new_status), strtolower($old_status), $current_date);
                return response()->json(
                    ["status"=>1,
                    "data"=>$data,
                    "message"=>"Transaction has been ".strtolower($new_status),
                    "new_status"=>$new_status
                ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);
            }

        }else{
            
            $data = Transaction::where("status","ONGOING")
            ->where("seller_id",$seller_id)
            ->orderBy('id', 'desc')->first();
            if ($data){
                $new_status = "DELIVERED";
                $old_status = "ONGOING";
            }
            else
            {
                $data = Transaction::where("status","PENDING")
                ->where("seller_id",$seller_id)
                ->orderBy('id', 'desc')->first();
                $new_status = "ONGOING";
                $old_status = "PENDING";
            }

            if ($data){
                $this->set_latest_status($data,$new_status,$old_status,$current_date);
                // TransactionSummaryController::update(false,$dsp, $data, strtolower($new_status), strtolower($old_status), $current_date);
                return response()->json(
                    ["status"=>1,
                    "data"=>$data,
                    "message"=>"Transaction has been ".strtolower($new_status),
                    "new_status"=>$new_status
                ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);
            }
        }

        return response()->json(["status"=>0,"data"=>$data,"message"=>"Transaction already completed"], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    private function set_latest_status($data,$new_status,$old_status,$current_date){
          if ($data){
            $old_status = strtolower($old_status);
            $new_status = strtolower($new_status);
            $dsp =  ServiceProvider::where("id",$data->service_provider_id)->first();
            $data->status=strtoupper($new_status);
            $data[$new_status."_date"] = Carbon::now();
            if($new_status=="completed"){
                $data->remitted_date = Carbon::now();
                DB::table("sellers")
                ->where("id",$data->seller_id)
                ->update(["sales_per_anum"=>DB::raw("sales_per_anum+".$data->base_price)]);
            }
            $data->save();

            $dsp =  ServiceProvider::where("id",$data->service_provider_id)->first();

            TransactionSummaryController::update(false,$dsp, $data, $new_status, $old_status, $current_date);
            return true;
        }
        return false;
    }


    public function execCommand(){
        Artisan::call('receipt:create');
    
        $url = "https://taxengine.psmed.org/files/mango.html";
        response()->json(["status"=>0,"website"=>$url,"message"=>"Success"], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);
    }
}

