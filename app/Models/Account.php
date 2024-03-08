<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Log;
use Illuminate\Support\Carbon;


class Account extends Model
{
    use HasFactory;

    protected $appends = [ 'avatar'];

    protected $fillable = ['user_id','account_number','first_name','middle_name','last_name'];

    protected $with = ["region"];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function serviceProvider()
    {
        return $this->hasMany(AccountServiceProvider::class,"account_id","id");
    }

    public function region()
    {
        return $this->hasMany(AccountRegion::class,"account_id","id");
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->timezone(config("timezone"))->format('d M Y H:i');
    }

    public function getAvatarAttribute()
    {
       return "/image/accounts/". $this->account_number;
    }

    public function delete()
    {
        $this->user()->delete();
        return parent::delete();
    }

    public static function boot() {
        parent::boot();

        static::deleting(function($account) { // before delete() method call this
            Log::info("delete here");
            $account->user()->delete();
             // do the rest of the cleanup...
        });
    }

}
