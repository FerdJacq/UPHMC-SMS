<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionCustomers extends Model
{
    use HasFactory;

    protected $fillable = ["transaction_id"];
    protected $appends = ["full_name"];

    public function getFullNameAttribute()
    {
        return strtoupper("{$this->first_name} {$this->last_name}");
    }
}
