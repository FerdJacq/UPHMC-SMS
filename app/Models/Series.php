<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Carbon;

class Series extends Model
{
    use HasFactory;

    protected $fillable = ['service_provider_id','company_code','prefix','suffix','series_no','complete_no','status'];

    public function ServiceProvider()
    {
        return $this->hasOne(ServiceProvider::class, 'id', 'service_provider_id');
    }

    public function getStatusAttribute($value)
    {
        return ($value==1) ? "AVAILABLE" : "USED";
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->timezone(config("timezone"))->format('Y-m-d H:i:s');
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->timezone(config("timezone"))->format('Y-m-d H:i:s');
    }
}
