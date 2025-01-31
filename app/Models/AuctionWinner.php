<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Action;

class AuctionWinner extends Model
{
    use HasFactory;

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }

    public function auction()
    {
        return $this->belongsTo(Auction::class, 'auction_id', 'id');
    }
}
