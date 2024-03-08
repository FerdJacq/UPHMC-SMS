<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionLogs extends Model
{
    use HasFactory;

    protected $fillable = ['transaction_id'];

    public function details()
    {
        return $this->hasMany(TransactionDetailLogs::class, 'transaction_log_id', 'id');
    }
}
