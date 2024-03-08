<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Whitelist extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'ip_address',
        'status'
    ];


    public function setNameAttribute($value)
    {
        $this->attributes['name'] = strtoupper($value);
    }

    public function setIpAddressAttribute($value)
    {
        $this->attributes['ip_address'] = strtoupper($value);
    }
}
