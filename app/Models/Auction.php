<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Auction extends Model
{
    use HasFactory;

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }


    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }

    public function offers()
    {
        return $this->hasMany(Offer::class, 'auction_id', 'id');
    }


    public function auctionWinners()
    {
        return $this->hasMany(AuctionWinner::class, 'auction_id', 'id');
    }
}
