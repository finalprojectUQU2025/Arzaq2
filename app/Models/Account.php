<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Passport\HasApiTokens;

class Account extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    public function imageProfile(): Attribute
    {
        return Attribute::make(
            get: fn() => is_null($this->image) ? asset('cms/dist/img/avatar2.png') : Storage::url($this->image)
        );
    }


    public function blockedSta(): Attribute
    {
        return new Attribute(
            get: fn() => match ($this->status) {
                'verefy' => 'فعال',
                'unVerefy' => 'الحساب غير فعال',
                'blocked' => 'محظور',
                default => 'غير معروف'
            }
        );
    }

    public function typeKey(): Attribute
    {
        return new Attribute(
            get: fn() => $this->type === 'mazarie' ? 'مزارع' : 'تاجر'
        );
    }

    public function contactUs()
    {
        return $this->hasMany(ContactUs::class, 'account_id', 'id');
    }


    public function auction()
    {
        return $this->belongsTo(Auction::class, 'account_id', 'id');
    }


    public function offer()
    {
        return $this->hasMany(Offer::class, 'account_id', 'id');
    }


    public function auctionWinner()
    {
        return $this->hasMany(AuctionWinner::class, 'account_id', 'id');
    }

    public function wallet()
    {
        return $this->hasMany(Wallet::class, 'account_id', 'id');
    }

    public function countries()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    public function transaction()
    {
        return $this->hasMany(Transaction::class, 'account_id', 'id');
    }

    public function notification()
    {
        return $this->hasMany(Notification::class, 'account_id', 'id');
    }
}
