<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;


class Account extends Model
{
    use HasFactory;

    protected $appends = [ 'avatar'];

    protected $fillable = ['user_id','account_number','first_name','middle_name','last_name'];

    public function user()
    {
        return $this->belongsTo(User::class);
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

        static::deleting(function($account) 
        { // before delete() method call this
            $account->user()->delete();
             // do the rest of the cleanup...
        });
    }

}
