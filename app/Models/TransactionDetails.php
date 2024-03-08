<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionDetails extends Model
{
    use HasFactory;

    protected $appends = ["image"];

    protected $fillable = [
        'transaction_id',
        'item',
        'variant',
        'qty',
        'unit_price',
        'total_price'
    ];

    public function transaction(){
        return $this->belongsTo(Transaction::class);
    }

    public function log(){
        return $this->hasOne(TransactionDetailLogs::class, 'transaction_detail_id', 'id');
    }

    public function getImageAttribute()
    {
       return "/image/transaction_details/". $this->id;
    }

    public function delete()
    {
        return parent::delete();
    }
}
