<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Admin;
use App\Models\Auction;
use App\Models\AuctionWinner;
use App\Models\ContactUs;
use App\Models\Notification;
use App\Models\Offer;
use App\Models\Transaction;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class DashbordController extends Controller
{
    //

    public function showDashbord()
    {
        $allAlmazarie = Account::where('type', '=', 'mazarie')->where('status', '=', 'verefy')->get();
        $allTajir = Account::where('type', '=', 'tajir')->where('status', '=', 'verefy')->get();
        $allAdmin = Admin::where('blocked', '=', false)->get();
        $allAuction = Auction::all();
        $data = ContactUs::orderBy('id', 'desc')->whereRaw('DATE(created_at) = CURRENT_DATE')->get();
        return view('cms.dashpard', [
            'allAlmazarie' => $allAlmazarie,
            'allTajir' => $allTajir,
            'allAdmin' => $allAdmin,
            'allAuction' => $allAuction,
            'data' => $data,
        ]);
    }

    public function closeExpiredAuctions()
    {
        try {
            $now = Carbon::now('Asia/Riyadh');
            $allAuctionsNotEndTime = Auction::where('end_time', '=', null)->where('status', 'waiting')->get();
            foreach ($allAuctionsNotEndTime as $auctionEndTim) {
                if ($auctionEndTim->product_id == 1) {
                    if ($now->between(Carbon::parse('09:00:00'), Carbon::parse('10:00:00'))) {
                        $auctionEndTim->end_time = $now->addMinutes(5);
                        $auctionEndTim->save();
                    }
                }

                if ($auctionEndTim->product_id == 2) {
                    if ($now->between(Carbon::parse('10:00:00'), Carbon::parse('11:00:00'))) {
                        $auctionEndTim->end_time = $now->addMinutes(5);
                        $auctionEndTim->save();
                    }
                }
            }

            $expiredAuctions = Auction::where('end_time', '<=', now())->where('status', 'waiting')->get();

            foreach ($expiredAuctions as $auction) {
                $highestOffer = Offer::where('auction_id', $auction->id)->orderBy('offer_amount', 'desc')->first();
                $currentTime = now();
                $endTime = Carbon::parse($auction->end_time);

                if ($highestOffer == null) {
                    if ($currentTime > $endTime) {
                        $auction->status = 'cancel';
                        $auction->save();
                    }
                } else {
                    if ($currentTime > $endTime) {
                        $auctionWinner = new AuctionWinner();
                        $auctionWinner->auction_id = $auction->id;
                        $auctionWinner->account_id = $highestOffer->account_id;
                        $auctionWinner->winning_amount = $highestOffer->offer_amount;
                        $auctionWinner->won_at = now();
                        $isSavesAuctionWinner = $auctionWinner->save();
                        if ($isSavesAuctionWinner) {
                            $notification = new Notification();
                            $notification->title = 'ربحت المزاد';
                            $notification->details = 'تهانيا لقد كسبت المزاد الخاص بالمنتج ' . ($auctionWinner->auction->product->name ?? '');
                            $notification->account_id = $highestOffer->account_id;
                            $notification->status = 'tajir';
                            $notification->name_moza = $auctionWinner->auction->account->name ?? '';
                            $notification->image_moza = url('storage/' . $auctionWinner->auction->account->image ?? '');
                            $notification->city_moza = $auctionWinner->auction->account->countries->name ?? '';
                            $isSavedNotification = $notification->save();
                        }

                        if ($isSavedNotification) {
                            $notification = new Notification();
                            $notification->title = 'اغلاق المزاد';
                            $notification->details = 'تهانينا! تم إغلاق المزاد الخاص بالمنتج ' . ($auctionWinner->auction->product->name ?? '') . ' والفائز بالمزاد هو ' . ($highestOffer->account->name ?? '');
                            $notification->account_id = $auction->account_id;
                            $notification->status = 'mazarie';
                            $isSavedNotification = $notification->save();
                        }
                        $auction->status = 'done';
                        $auction->save();
                        $highestOffer->status = 'accepted';
                        $isEndSaved = $highestOffer->save();
                        if ($isEndSaved) {

                            $sellerWallet = Wallet::where('account_id', '=', $auction->account->id)->first();
                            $buyerWallet =  Wallet::where('account_id', '=', $highestOffer->account_id)->first();
                            if ($buyerWallet && $buyerWallet->balance >= $highestOffer->offer_amount) {
                                $buyerWallet->balance -= $highestOffer->offer_amount;
                                $isSaved = $buyerWallet->save();
                                if ($isSaved) {
                                    $transaction = new Transaction();
                                    $transaction->wallet_id = $buyerWallet->id;
                                    $transaction->account_id = Auth::id();
                                    $transaction->type = 'withdrawal';
                                    $transaction->amount = $highestOffer->offer_amount;
                                    $transaction->description = 'خصم مبلغ المزاد';
                                    $isSavd = $transaction->save();
                                }

                                if ($isSavd) {
                                    $sellerWallet->balance += $highestOffer->offer_amount;
                                    $isSavedSeller = $sellerWallet->save();
                                    if ($isSavedSeller) {
                                        $transaction = new Transaction();
                                        $transaction->wallet_id = $sellerWallet->id;
                                        $transaction->account_id = Auth::id();
                                        $transaction->type = 'deposit';
                                        $transaction->amount = $highestOffer->offer_amount;
                                        $transaction->description = 'إيداع مبلغ المزاد';
                                        $transaction->save();
                                    }
                                }
                            } else {
                                return response()->json([
                                    'status' => false,
                                    'message' => 'رصيد المشتري غير كافٍ لإكمال الصفقة.',
                                ], Response::HTTP_BAD_REQUEST);
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Error closing auctions: ' . $e->getMessage());
        }
    }
}
