<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Messages;
use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\AuctionWinner;
use App\Models\Notification;
use App\Models\Offer;
use App\Models\Transaction;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class OfferController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $auctionId)
    {
        $validator = Validator::make($request->all(), [
            'offer_amount' => 'required|numeric|min:0',
        ]);
        if (!$validator->fails()) {

            try {
                $auction = Auction::where('id', '=', $auctionId)->first();
                $latestOffer = Offer::where('auction_id', $auctionId)->latest('created_at')->first();
                $currentTime = now();
                $endTime = $auction->end_time;
                $productId = $auction->product_id;

                if ($productId == 1 && !($currentTime->between(Carbon::parse('09:00:00'), Carbon::parse('10:00:00')))) {
                    return response()->json([
                        'statue' => false,
                        'message' => 'يمكنك تقديم العرض  للخضروات فقط بين الساعة 9:00 و 10:00.',
                    ], Response::HTTP_BAD_REQUEST);
                }
                if ($productId == 2 && !($currentTime->between(Carbon::parse('10:00:00'), Carbon::parse('11:00:00')))) {
                    return response()->json([
                        'statue' => false,
                        'message' => 'يمكنك تقديم العرض للفواكه  فقط بين الساعة 10:00 و 11:00.',
                    ], Response::HTTP_BAD_REQUEST);
                }

                $isWallet  = Wallet::where('account_id', '=', Auth::id())->first();

                if ($latestOffer == null) {
                    if ($currentTime < $endTime) {
                        if ($isWallet->balance >= $request->get('offer_amount')) {

                            if ($request->get('offer_amount') > $auction->starting_price) {
                                $offer = new Offer();
                                $offer->account_id = Auth::id();
                                $offer->auction_id = $auctionId;
                                $offer->offer_amount = $request->get('offer_amount');
                                $isSaved = $offer->save();
                                return response()->json(
                                    [
                                        'statue' => $isSaved ? true : false,
                                        'message' => $isSaved ?  'لقد تم تفديم العرض الخاص بك' : 'حدث خطأ ما',
                                        'data' =>  [
                                            'id' => $offer->id ?? '',
                                            'image' => url('storage/' . $offer->account->image) ?? '',
                                            'name' => $offer->account->name ?? '',
                                            'city' => $offer->account->countries->name ?? '',
                                            'offerAmount' => $offer->offer_amount . ' ر.س' ?? '',
                                        ],
                                    ],
                                    Response::HTTP_OK
                                );
                            } else {
                                return response()->json(
                                    [
                                        'statue' =>  false,
                                        'message' => 'يجب ان  تكون قيمة العرض اعلى  من السعر الافتراضي',
                                    ],
                                    Response::HTTP_BAD_REQUEST
                                );
                            }
                        } else {
                            return response()->json(
                                [
                                    'statue' =>  false,
                                    'message' => 'عذرا لا يوجد لديك رصيد كافي الرجاء شحن الرصيد',
                                ],
                                Response::HTTP_BAD_REQUEST
                            );
                        }
                    } else {
                        if ($latestOffer !== null) {
                            $latestOffer->status = 'closed';
                            $latestOffer->save();
                        }
                        return response()->json(
                            [
                                'statue' =>  false,
                                'message' => 'لقد تجاوزت المزاد الوقت المحدد له',
                            ],
                            Response::HTTP_BAD_REQUEST
                        );
                    }
                } else {

                    if ($currentTime < $endTime) {

                        if ($request->offer_amount <= $latestOffer->offer_amount) {
                            return response()->json([
                                'status' => false,
                                'message' => 'يجب أن يكون عرضك أعلى من أعلى عرض حالي.',
                            ], Response::HTTP_BAD_REQUEST);
                        } else {
                            $currentTime = now();
                            $endTime = Carbon::parse($auction->end_time);
                            $remainingTimeInSeconds = $currentTime->diffInSeconds($endTime);
                            if ($remainingTimeInSeconds <= 30) {
                                $newEndTime = $endTime->addSeconds(30);
                                $auction->end_time = $newEndTime;
                                $auction->save();
                                if ($isWallet->balance >= $request->get('offer_amount')) {
                                    if ($request->get('offer_amount') > $auction->starting_price) {
                                        $offer = new Offer();
                                        $offer->account_id = Auth::id();
                                        $offer->auction_id = $auctionId;
                                        $offer->offer_amount = $request->get('offer_amount');
                                        $isSaved = $offer->save();
                                        $latestOffer->status = 'closed';
                                        $latestOffer->save();
                                        return response()->json(
                                            [
                                                'statue' => $isSaved ? true : false,
                                                'message' => $isSaved ?  'لقد تم تفديم العرض الخاص بك' : 'حدث خطأ ما',
                                                'data' =>  [
                                                    'id' => $offer->id ?? '',
                                                    'image' => url('storage/' . $offer->account->image) ?? '',
                                                    'name' => $offer->account->name ?? '',
                                                    'city' => $offer->account->countries->name ?? '',
                                                    'offerAmount' => $offer->offer_amount . ' ر.س' ?? '',
                                                ],
                                            ],
                                            Response::HTTP_OK
                                        );
                                    } else {
                                        return response()->json(
                                            [
                                                'statue' =>  false,
                                                'message' => 'يجب ان  تكون قيمة العرض اعلى  من السعر الافتراضي',
                                            ],
                                            Response::HTTP_BAD_REQUEST
                                        );
                                    }
                                } else {
                                    return response()->json(
                                        [
                                            'statue' =>  false,
                                            'message' => 'عذرا لا يوجد لديك رصيد كافي الرجاء شحن الرصيد',
                                        ],
                                        Response::HTTP_BAD_REQUEST
                                    );
                                }
                            } else {
                                if ($isWallet->balance >= $request->get('offer_amount')) {
                                    if ($request->get('offer_amount') > $auction->starting_price) {
                                        $offer = new Offer();
                                        $offer->account_id = Auth::id();
                                        $offer->auction_id = $auctionId;
                                        $offer->offer_amount = $request->get('offer_amount');
                                        $isSaved = $offer->save();
                                        $latestOffer->status = 'closed';
                                        $latestOffer->save();
                                        return response()->json(
                                            [
                                                'statue' => $isSaved ? true : false,
                                                'message' => $isSaved ?  'لقد تم تفديم العرض الخاص بك' : 'حدث خطأ ما',
                                                'data' =>  [
                                                    'id' => $offer->id ?? '',
                                                    'image' => url('storage/' . $offer->account->image) ?? '',
                                                    'name' => $offer->account->name ?? '',
                                                    'city' => $offer->account->countries->name ?? '',
                                                    'offerAmount' => $offer->offer_amount . ' ر.س' ?? '',
                                                ],
                                            ],
                                            Response::HTTP_OK
                                        );
                                    } else {
                                        return response()->json(
                                            [
                                                'statue' =>  false,
                                                'message' => 'يجب ان  تكون قيمة العرض اعلى  من السعر الافتراضي',
                                            ],
                                            Response::HTTP_BAD_REQUEST
                                        );
                                    }
                                } else {
                                    return response()->json(
                                        [
                                            'statue' =>  false,
                                            'message' => 'عذرا لا يوجد لديك رصيد كافي الرجاء شحن الرصيد',
                                        ],
                                        Response::HTTP_BAD_REQUEST
                                    );
                                }
                            }
                        }
                    } else {
                        return response()->json(
                            [
                                'statue' =>  false,
                                'message' => 'لقد تجاوزت المزاد الوقت المحدد له',
                            ],
                            Response::HTTP_BAD_REQUEST
                        );
                    }
                }
            } catch (\Exception $e) {
                Log::error('حدث خطأ ما : ' . $e->getMessage());
            }
        } else {
            return response()->json(
                [
                    'statue' =>  false,
                    'message' => $validator->getMessageBag()->first(),
                ],
                Response::HTTP_BAD_REQUEST
            );
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Offer $offer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Offer $offer)
    {
        //
    }


    public function endAuction($auctionId)
    {
        try {
            $auction = Auction::findOrFail($auctionId);
            $currentTime = now();
            if ($currentTime < $auction->end_time) {
                return response()->json([
                    'status' => false,
                    'message' => 'المزاد لم ينته بعد.'
                ], 400);
            }
            $highestOffer = Offer::where('auction_id', $auctionId)->orderBy('offer_amount', 'desc')->first();
            if ($highestOffer) {
                DB::table('auction_winners')->insert([
                    'auction_id' => $auction->id,
                    'account_id' => $highestOffer->account_id,
                    'winning_amount' => $highestOffer->offer_amount,
                    'won_at' => now(),
                ]);
                $auction->status = 'done';
                $auction->save();
                $highestOffer->status = 'accepted';
                $highestOffer->save();
                return response()->json([
                    'status' => true,
                    'message' => 'تم إنهاء المزاد وتسجيل الفائز بنجاح.'
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'لم يتم تقديم أي عروض لهذا المزاد.'
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ أثناء إنهاء المزاد: ' . $e->getMessage(),
            ], 500);
        }
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
        } catch (\Exception $e) {
            Log::error('Error closing auctions: ' . $e->getMessage());
        }
    }


    public function closeMobileAuctions()
    {
        $now = Carbon::now('Asia/Riyadh');
        $allAuctionsNotEndTime = Auction::where('status', 'waiting')->get();
        foreach ($allAuctionsNotEndTime as $auction) {
            if ($auction->product_id == 1) {
                if ($now->format('H:i') === '10:00') {
                    if ($auction->status != 'cancel') {
                        $highestOffer = Offer::where('auction_id', $auction->id)->orderBy('offer_amount', 'desc')->first();
                        if ($highestOffer == null) {
                            $auction->status = 'cancel';
                            $isSavedClose = $auction->save();
                            $notification = new Notification();
                            $notification->title = 'اغلاق المزاد';
                            $notification->details = 'نأسف! لقد تم إغلاق المزاد دون مزايدة.';
                            $notification->account_id = $auction->account_id;
                            $notification->status = 'mazarie';
                            $notification->save();
                        } else {
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
                                $notification->city_moza = $auctionWinner->auction->countries->name ?? '';
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

                        return response()->json(
                            [
                                'status' => true,
                                'message' => 'لقد تم اغلاق المزاد بنجاح.'
                            ],
                            Response::HTTP_OK,
                        );
                    } else {
                        return response()->json(
                            [
                                'status' => false,
                                'message' => 'عذرًا، لقد تم إغلاق المزاد مسبقًا.'
                            ],
                            Response::HTTP_BAD_REQUEST
                        );
                    }
                }
            } elseif ($auction->product_id == 2) {
                if ($now->format('H:i') === '11:00') {
                    if ($auction->status != 'cancel') {
                        $highestOffer = Offer::where('auction_id', $auction->id)->orderBy('offer_amount', 'desc')->first();
                        if ($highestOffer == null) {
                            $auction->status = 'cancel';
                            $isSavedClose = $auction->save();
                            $notification = new Notification();
                            $notification->title = 'اغلاق المزاد';
                            $notification->details = 'نأسف! لقد تم إغلاق المزاد دون مزايدة.';
                            $notification->account_id = $auction->account_id;
                            $notification->status = 'mazarie';
                            $notification->save();
                        } else {
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
                                $notification->city_moza = $auctionWinner->auction->countries->name ?? '';
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

                        return response()->json(
                            [
                                'status' => true,
                                'message' => 'لقد تم اغلاق المزاد بنجاح.'
                            ],
                            Response::HTTP_OK,
                        );
                    } else {
                        return response()->json(
                            [
                                'status' => false,
                                'message' => 'عذرًا، لقد تم إغلاق المزاد مسبقًا.'
                            ],
                            Response::HTTP_BAD_REQUEST
                        );
                    }
                }
            }
        }
    }
}
