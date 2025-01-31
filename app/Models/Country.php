<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Country extends Model
{
    use HasFactory;


    public function imageProfile(): Attribute
    {
        return Attribute::make(
            get: fn() => is_null($this->image) ? asset('cms/dist/img/user2-160x160.jpg') : Storage::url($this->image)
        );
    }


    public function activeKey(): Attribute
    {
        return new Attribute(get: fn() => $this->active ? 'فعال' : 'غير فعال');
    }

    public function admins()
    {
        return $this->hasMany(Admin::class, 'country_id', 'id');
    }

    public function account()
    {
        return $this->hasMany(Account::class, 'country_id', 'id');
    }
}
