<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'wallet_id', 'id');
    }
}
