<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

use App\Models\ServiceProvider;
use App\Models\Transaction;
use App\Models\TransactionDetails;
use App\Models\Seller;
use App\Models\TransactionLogs;

use Illuminate\Support\Carbon;

use Auth;
use Helper;
use DB;
use Excel;

use App\Exports\TransactionExport;

class TransactionController extends Controller
{

    public function index()
    {
        return Inertia::render('transaction/index');
    }

    public function list(Request $request)
    {
        // $request->status = ($request->selected_date_type=="REMITTED") ? "COMPLETED" : $request->status;   
        $date_type = strtolower($request->selected_date_type);
        $start_date = $request->start_date ? date('Y-m-d', strtotime($request->start_date)) : "";
        $end_date =  $request->end_date ? date('Y-m-d', strtotime($request->end_date)) : "";
        $search = $request->search;
        $request->status = collect($request->status)->filter()->all();
        $selected_dsp = $request->selected_dsp ? $request->selected_dsp :[];

        $seller = DB::table("sellers")->where("tin","029203920132")->first();

        $data = Transaction::with(['ServiceProvider','log.details'])->when($request->sort_by, function ($query, $value) {
            $query->orderBy($value, request('order_by', 'asc'));
        })
        ->when(!isset($request->sort_by), function ($query) {
            $query->latest();
        })
        ->where(function($query) use ($search) {
            if ($search)
            {
                $query->where('trans_id', 'LIKE', '%'.$search.'%')
                ->orWhere("or_number",'LIKE','%'.$search.'%')
                ->orWhere("reference_number",'LIKE','%'.$search.'%');
            }
        })
        ->when($request->selected_date_type && $start_date && $end_date, function ($query, $value) use($date_type,$start_date,$end_date) {
            $query->whereBetween(DB::raw('DATE('.$date_type.')'), [$start_date, $end_date]);
        })
        ->when(Helper::auth_role_dsp(),function($query){
            $query->whereIn("service_provider_id",Helper::auth_dsp_list("service_provider_id"));
        })
        ->when(Helper::auth_role_seller(),function($query) use($seller){
            $seller_id = ($seller) ? $seller->id : 0;
           $query->whereIn("service_provider_id",Helper::auth_dsp_list("service_provider_id"))
            ->where("seller_id",$seller_id);
        })
        ->when(Helper::auth_role_rdo(),function($query) use($seller){
           $query->whereIn("region_code",Helper::auth_region_list());
        })
        ->when($request->selected_regions,function($query) use($request){
            $query->whereIn("region_code",$request->selected_regions);
        })
        ->when(isset($request->status) && count($request->status)>0,function($query) use($request){
            $query->whereIn("status",$request->status);
        })
        ->when($selected_dsp,function($query) use($selected_dsp) {
            $query->whereIn("service_provider_id",$selected_dsp);
        })
        ->when(in_array($request->selected_type,[1,2]),function($q) use ($request){
            if($request->selected_type==1)
                $q->whereNotNull("remitted_date");
            else
                $q->whereNull("remitted_date");
        })
        ->orderByDesc("id")
        ->paginate($request->page_size ?? 10);
       

        return response()->json(["status"=>"success","data"=>$data], 200);
    }

    public function get($id)
    {
        $data = Transaction::with(["ServiceProvider","details.log","customer","seller",'log.details'])->where("id",$id)->first();
        $url = ($data->blockchain_trx_id) ? env("KALEIDO_FIREFLY_EXPLORER")."/".$data->blockchain_trx_id."?time=24hours&slide=".$data->blockchain_block_number : "";
        $blockchain = [
            "username"=>env("KALEIDO_USERNAME"),
            "password"=>env("KALEIDO_PASSWORD"),
            "blockchain_url"=>$url
        ];
        $data->blockchain = $blockchain;
        return response()->json(["status"=>"success","data"=>$data], 200);
    }

    public function update($id,Request $request)
    {
        $transaction = Transaction::with(["details.log","serviceProvider.fees"])->where("id",$id)->first();

        $items = collect($request->items)->map(function ($item) use($transaction) {
            $item["transaction_id"] = $transaction->id;
            $item["transaction_detail_id"] = $item["id"];
            $item['total_price'] = $item['qty'] * $item['unit_price'];
            return $item;
        });

        $total = collect($items)->sum("total_price");
        $additional_fees = $transaction->shipping_fee;
        $discounts = $transaction->voucher + $transaction->coins;
        $net = ($total + $additional_fees) - $discounts;
        $subtotal = ($total  - $discounts);

        $fee_type = ["TRANSACTION","SERVICE","COMMISSION"];
        $fee_list = $transaction->serviceProvider->fees; //array of collection
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
        $shipping_vat = ($transaction->shipping_fee / 1.12) * 0.12;
        $product_less_discount = $subtotal;

        if ($transaction->vat_type=="NV"){
            $base_price = $product_less_discount;
            $item_vat = 0;
        }else{
            $base_price = ($product_less_discount / 1.12);
            $item_vat = $base_price * 0.12;
        }
        
        $withholding_tax = $base_price * 0.005;
        $tax = $online_platform_vat + $shipping_vat + $item_vat + $withholding_tax;

        $data = TransactionLogs::firstOrNew(['transaction_id' => $id]);
        $data->transaction_id = $transaction->id;
        $data->shipping_fee = $transaction->shipping_fee;
        $data->voucher = $transaction->voucher;
        $data->coins = $transaction->coins;
        $data->subtotal_amount = $total;
        $data->total_amount = $net;
        $data->transaction_fee = $computed_fee[0];
        $data->service_fee = $computed_fee[1];
        $data->commission_fee = $computed_fee[2];
        $data->online_platform_vat = $online_platform_vat;
        $data->shipping_vat = $shipping_vat;
        $data->item_vat = $item_vat;
        $data->base_price = $base_price;
        $data->withholding_tax = $withholding_tax;
        $data->tax = $tax;
        $data->is_seen = 0;
        $data->save();
        $data->details()->delete();
        $data->details()->createMany($items);

        $data = Transaction::with(["ServiceProvider","details.log","customer","seller",'log.details'])->where("id",$id)->first();
        $url = ($data->blockchain_trx_id) ? env("KALEIDO_FIREFLY_EXPLORER")."/".$data->blockchain_trx_id."?time=24hours&slide=".$data->blockchain_block_number : "";
        $blockchain = [
            "username"=>env("KALEIDO_USERNAME"),
            "password"=>env("KALEIDO_PASSWORD"),
            "blockchain_url"=>$url
        ];
        $data->blockchain = $blockchain;

        return response()->json(["status"=>"success","data"=>$data], 200);
    }

    public function seen($id){
        // TransactionLogs::where("id",$id)->update(["is_seen"=>1]);
        TransactionLogs::where("is_seen",0)->update(["is_seen"=>1]);
        return response()->json(["status"=>"success"], 200); 
    }

    public function generate_report(Request $request)
    {
        $data = $this->validate($request, [
            'start_date' => 'required',
            'end_date' => 'required',
            'selected_date_type' => 'required|string',
        ]);

        $date_type = strtolower($request->selected_date_type."_date");
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $ref = Helper::ref_number("TG",20,"-");
        $filename = 'excel/'.$ref.'-transaction.xlsx';
        $params = new TransactionExport($request);
        

        Excel::store($params, $filename ,'storage');
    
        return response()->json(['file' => $filename]);

    }

    public function viewReceipt($id)
    {
        // $id = decrypt(123);
        $data = Transaction::with('ServiceProvider','log.details','seller','customer')->where("id",$id)->first();

        return view('online_receipt')->with('data', $data);
    }
}
