<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Condition extends Model
{
    use HasFactory;

    public function activeKey(): Attribute
    {
        return new Attribute(get: fn() => $this->active ? 'فعال' : 'غير فعال');
    }
}
