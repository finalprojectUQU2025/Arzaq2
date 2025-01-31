<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    public function imageProfile(): Attribute
    {
        return Attribute::make(
            get: fn() => is_null($this->image) ? asset('cms/dist/img/adminImage.jpg') : Storage::url($this->image)
        );
    }


    public function blockedSta(): Attribute
    {
        return new Attribute(get: fn() => $this->blocked ? 'محظور' : 'غير محظور');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }
}
