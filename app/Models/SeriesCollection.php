<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeriesCollection extends Model
{
    use HasFactory;

    protected $fillable = ["account_id","service_provider_id","company_code","prefix","suffix","starting_no","ending_no","length"];

    public function ServiceProvider()
    {
        return $this->hasOne(ServiceProvider::class, 'id', 'service_provider_id');
    }
}
