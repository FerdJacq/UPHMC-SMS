<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{
    use HasFactory;
    protected $fillable = ["service_provider_id","region_code","registered_name","registered_address","business_name","tin","vat_type","contact_number","email","type","sales_per_anum","eligible_witheld_seller","has_cor"];

    protected $with = ["region"];
    public function serviceProvider()
    {
        return $this->hasMany(SellerServiceProvider::class,"seller_id","id");
    }

    public function transaction()
    {
        return $this->hasMany(Transaction::class,"seller_id","id");
    }

    public function region()
    {
        return $this->hasOne(Region::class,"region_code","region_code");
    }
    
    public function files()
    {
        return $this->hasMany(SellersFile::class,"sellers_id","id");
    }
}