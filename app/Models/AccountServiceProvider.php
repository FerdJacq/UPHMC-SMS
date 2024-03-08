<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountServiceProvider extends Model
{
    use HasFactory;

    protected $fillable = ['account_id','service_provider_id'];

    
    public function account()
    {
        return $this->hasOne(Account::class,"id","account_id");
    }

    public function data()
    {
        return $this->hasOne(ServiceProvider::class,"id","service_provider_id");
    }
}
