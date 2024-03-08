<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountRegion extends Model
{
    use HasFactory;

    protected $fillable = ["account_id","region_code"];

    protected $with = ["region"];

    public function region()
    {
        return $this->hasOne(Region::class,"region_code","region_code");
    }
}
