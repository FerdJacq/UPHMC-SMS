<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerServiceProvider extends Model
{
    use HasFactory;

    protected $fillable = ['seller_id','service_provider_id'];
}
