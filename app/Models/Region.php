<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use HasFactory;

    protected $appends = ["label"];

    protected $hidden = [
        'created_at','updated_at',
    ];
    
    public function scopeOrderName($query)
    {
        return $query->orderBy('name', 'asc');
    }

    public function getNameAttribute($value)
    {
        return ucfirst(($value));
    }   
    public function getLabelAttribute()
    {
        return $this->name . "($this->region_desc)";
    }   
}
