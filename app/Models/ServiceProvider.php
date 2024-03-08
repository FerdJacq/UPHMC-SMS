<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class ServiceProvider extends Model
{
    use HasFactory;

    protected $appends = ['logo'];

    protected $hidden = ["token","secret","code"];

    protected $fillable = [
        'reference_number',
        'code',
        'token',
        'secret',
        'email',
        'company_name',
        'category',
        'registration_notified',
        'status',
        'company_code',
        'address',
        'tin'
    ];

    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = strtoupper($value);
    }

    public function setCompanyNameAttribute($value)
    {
        $this->attributes['company_name'] = strtoupper($value);
    }

    public function setRegistrationNotifiedAttribute($value)
    {
        $this->attributes['registration_notified'] = strtoupper($value);
    }

    public function setCategoryAttribute($value)
    {
        $this->attributes['category'] = strtoupper($value);
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->timezone(config("timezone"))->format('d M Y H:i');
    }

    public function getLogoAttribute()
    {
       return "/image/service_provider/". $this->reference_number;
    }

    public function fees()
    {
        return $this->hasMany(Fees::class, 'service_provider_id', 'id');
    }
}
