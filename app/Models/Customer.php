<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $fillable = ["service_provider_id","first_name","middle_name","last_name","birth_date"];
    protected $appends = ["full_name"];

    public function getFullNameAttribute()
    {
        return strtoupper("{$this->first_name} {$this->last_name}");
    }
}
