<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id', // إضافة هذا الحقل
        'auction_id',
        'offer_amount',
        'status',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }

    public function auction()
    {
        return $this->belongsTo(Auction::class, 'auction_id', 'id');
    }
}
