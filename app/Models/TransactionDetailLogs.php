<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class TransactionDetailLogs extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'transaction_log_id',
        'transaction_detail_id',
        'item',
        'variant',
        'qty',
        'unit_price',
        'total_price'
    ];


    public function transaction(){
        return $this->hasOne(Transaction::class, 'id', 'transaction_id');
    }
    
    public function log(){
        return $this->hasOne(Transaction::class, 'id', 'transaction_log_id');
    }

    public function transactionDetail(){
        return $this->hasOne(Transaction::class, 'id', 'transaction_detail_id');
    }

    public function getImageAttribute()
    {
       return "/image/transaction_details/". $this->transaction_detail_id;
    }

    public function delete()
    {
        return parent::delete();
    }
}
