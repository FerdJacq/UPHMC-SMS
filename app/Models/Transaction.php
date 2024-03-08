<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Transaction extends Model
{
    use HasFactory;

    public function details()
    {
        return $this->hasMany(TransactionDetails::class, 'transaction_id', 'id');
    }

    public function customer()
    {
        return $this->hasOne(Customer::class, 'id', 'customer_id');
    }

    public function seller()
    {
        return $this->hasOne(Seller::class, 'id', 'seller_id');
    }

    public function log()
    {
        return $this->hasOne(TransactionLogs::class, 'transaction_id', 'id');
    }

    public function ServiceProvider()
    {
        return $this->hasOne(ServiceProvider::class, 'id', 'service_provider_id');
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->timezone(config("timezone"))->format('d M Y H:i');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'COMPLETED');
    }
}
