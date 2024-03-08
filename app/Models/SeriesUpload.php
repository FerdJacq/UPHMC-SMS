<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeriesUpload extends Model
{
    use HasFactory;

    public function ServiceProvider()
    {
        return $this->hasOne(ServiceProvider::class, 'id', 'service_provider_id');
    }
}
