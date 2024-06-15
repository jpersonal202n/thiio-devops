<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'last_name',
        'phone',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Hash::make($password);
    }


    //TODO: change to trait to global method
    public function scopeWhereFindUuid($query,$uuid) 
    {
        if (!$uuid) {
            return $query;
        }

        $query->where('uuid',$uuid);
    }

    public function scopeWhereFindUuidFirstOrFail($query,$uuid)
    {
        return $query->whereFindUuid($uuid)->firstOrFail();
    }

    public function scopeWhereFindUuidFirst($query,$uuid)
    {
        return $query->whereFindUuid($uuid)->first();
    }

    public function scopeSearch($query,$searcher)
    {
        if (!$searcher) {
            return $query;
        } 

        return $query->where('name','like',"%{$searcher}%")
            ->OrWhere('last_name','like',"%{$searcher}%")
            ->orWhere('email','like',"%{$searcher}%");
    }
}
