<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionSummary extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id','service_provider_id','transaction_fee','service_fee','commission_fee',
        'online_platform_vat','shipping_vat','item_vat','withholding_tax','tax',
        'pending','ongoing','delivered','cancelled','refunded','completed','date','assigned_date','region_code',
        'type','vat_type','base_price','total_amount'
    ];

    public function ServiceProvider()
    {
        return $this->hasOne(ServiceProvider::class, 'id', 'service_provider_id');
    }

    public function region()
    {
        return $this->hasOne('App\Models\Region', 'region_code', 'region_code');
    }
}
