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
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class TajirController extends Controller
{
    //دالة عرض صفحة الرئيسية
    public function homeScreen($id)
    {
        $data = Auction::where('status', '=', 'waiting')->where('product_id', '=', $id)->get()->map(function ($auction) {
            return [
                'id' => $auction->id ?? '',
                'name' => $auction->name ?? '',
                'image' => url('storage/',  $auction->image) ?? '',
                'quantity' => $auction->quantity ?? '',
            ];
        });

        // $allTransactions = Offer::join('auctions', 'offers.auction_id', '=', 'auctions.id') // ربط جدول offers مع auctions
        //     ->where('offers.account_id', Auth::id()) // المستخدم الحالي
        //     ->where('auctions.product_id', $id) // شرط المنتج من جدول auctions
        //     ->whereMonth('offers.created_at', Carbon::now()->month) // الشهر الحالي من جدول offers
        //     ->whereYear('offers.created_at', Carbon::now()->year) // السنة الحالية من جدول offers
        //     ->distinct('offers.auction_id') // حذف التكرار بناءً على auction_id
        //     ->count('offers.auction_id'); // حساب عدد المزادات التي شارك فيها

        $allTransactions = Offer::where('account_id', Auth::id())
            ->whereMonth('created_at', Carbon::now()->month) // الشهر الحالي
            ->whereYear('created_at', Carbon::now()->year) // السنة الحالية
            ->distinct('auction_id') // حذف التكرار بناءً على auction_id
            ->count('auction_id');

        $allBalance = Wallet::where('account_id', '=', Auth::id())->first();

        return response()->json([
            'status' => true,
            'message' => Messages::getMessage('SUCCESS_SEND'),
            'allData' => [
                'balance' => $allBalance->balance . ' ' . 'ر.س'  ?? '', //الرصيد
                'transactions' => $allTransactions ?? '',
                'data' => $data ?? '',
            ],
        ]);
    }

    //دالة عرض صفحة المشتريات
    public function myPurchases()
    {
        $data = AuctionWinner::where('account_id', '=', Auth::id())->get()->map(function ($auctionWinner) {
            return [
                'id' => $auctionWinner->auction->id ?? '',
                'name' => $auctionWinner->auction->name ?? '',
                'image' => url('storage/',  $auctionWinner->auction->image) ?? '',
                'quantity' => $auctionWinner->auction->quantity ?? '',
                'account' => [
                    'id' => $auctionWinner->auction->account->id,
                    'name' => $auctionWinner->auction->account->name,
                ],
                'price' => $auctionWinner->winning_amount . ' ' . 'ر.س' ?? '',
                'created_at' => Carbon::parse($auctionWinner->won_at)->format('d/m/Y - h:i') . (Carbon::parse($auctionWinner->won_at)->format('A') == 'AM' ? ' ص' : ' م'),

            ];
        });
        $allBalance = Wallet::where('account_id', '=', Auth::id())->first();
        return response()->json([
            'status' => true,
            'message' => Messages::getMessage('SUCCESS_SEND'),
            'allData' => [
                'balance' => $allBalance->balance . ' ' . 'ر.س'  ?? '', //الرصيد
                'purchases' => $data->count(),
                'data' => $data,
            ],
        ]);
    }

    //دالة عرض صفحة تفاصيل المزاد
    public function auctionDetails(Auction $auction)
    {
        $offers = Offer::where('auction_id', $auction->id)->get()->map(function ($offer) {
            return [
                'id' => $offer->id ?? '',
                'image' => url('storage/' . $offer->account->image) ?? '',
                'name' => $offer->account->name ?? '',
                'city' => $offer->account->countries->name ?? '',
                'offerAmount' => $offer->offer_amount . ' ر.س' ?? '',
            ];
        });

        $highestOffer = Offer::where('auction_id', $auction->id)
            ->orderBy('offer_amount', 'desc')
            ->first();

        return response()->json([
            'status' => true,
            'message' => Messages::getMessage('SUCCESS_SEND'),
            'data' => [
                'id' => $auction->id ?? '',
                'image' => url('storage/' . $auction->image) ?? '',
                'name' => $auction->name ?? '',
                'quantity' => $auction->quantity ?? '',
                'starting_price' => $auction->starting_price . ' ر.س' ?? '',
                'mazarieAccount' => [
                    'id' => $auction->account->id,
                    'image' => url('storage/' . ($auction->account->image ?? '')) ?? '',
                    'name' => $auction->account->name,
                ],
                'totalOffer' => $offers->count(),
                'highestOffer' => $highestOffer ? [ // تحقق من عدم كون highestOffer null
                    'id' => $highestOffer->id ?? '',
                    'image' => url('storage/' . ($highestOffer->account->image ?? '')) ?? '',
                    'name' => $highestOffer->account->name ?? '',
                    'city' => $highestOffer->account->countries->name ?? '',
                    'highestOffer' => $highestOffer->offer_amount . ' ر.س' ?? '',
                ] : '', // إذا لم يكن هناك عروض، اجعلها null
                'allOffer' => $offers ?? '',
            ],
        ]);
    }


    //دالة عرض صفحة تفاصيل محفظتي
    public function myWallet()
    {
        $myWallet = Wallet::where('account_id', '=', Auth::id())->first();

        $myTransaction = Transaction::where('Wallet_id', '=', $myWallet->id)->get()->map(function ($transaction) {
            return [
                'id' => $transaction->id,
                'image' => url('storage/' . $transaction->account->image ?? ''),
                'name' => $transaction->account->name ?? '',
                'created_at' => \Carbon\Carbon::parse($transaction->created_at)->format('d/m/Y - h:i A'),
                'amount' => ($transaction->type == 'withdrawal' ? '-' : '+') . ' ' . $transaction->amount . ' ' . 'ر.س' ?? '',
            ];
        });

        return response()->json([
            'status' => true,
            'message' => Messages::getMessage('SUCCESS_SEND'),
            'data' => [
                'balance' => $myWallet->balance . ' ' . 'ر.س' ?? '',
                'cardExpiry' =>  $myWallet->card_expiry ?? '',
                'cardNumber' =>  $myWallet->card_number ?? '',
                'myTransaction' => $myTransaction,
            ]
        ]);
    }


    //دالة  شحن الرصيد
    public function addWallet(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'card_holder_name' => 'required|string|min:3|max:255',
            'card_number' => 'required|string|size:16',
            'card_expiry' => 'required|string|date_format:m/y',
            'card_cvv' => 'required|string|size:3',
            'balance' => 'required|numeric|min:0',
        ]);
        if (!$validator->fails()) {
            $wallet = Wallet::firstOrNew(['account_id' => Auth::id()]); // استخدام firstOrNew
            $wallet->card_holder_name = $request->get('card_holder_name');
            $wallet->card_number = $request->get('card_number');
            $wallet->card_expiry = $request->get('card_expiry');
            $wallet->card_cvv = $request->get('card_cvv');
            // إذا كانت المحفظة جديدة، قم بتعيين الرصيد. إذا كانت موجودة، أضف الرصيد.
            if ($wallet->exists) {
                $wallet->balance += $request->get('balance'); // إضافة الرصيد
            } else {
                $wallet->balance = $request->get('balance'); // تعيين الرصيد
            }
            $isSaved = $wallet->save();
            if ($isSaved) {
                $transaction = new Transaction();
                $transaction->wallet_id = $wallet->id;
                $transaction->account_id = Auth::id();
                $transaction->type = 'deposit';
                $transaction->amount = $request->get('balance');
                $transaction->description = 'تم شحن المحفظة';
                $isSavedTransaction = $transaction->save();
            }
            return response()->json([
                'status' => $isSaved,
                'message' => $isSaved ? 'لقد تم  انشاء وشحن المحفظة الخاصة بك' : 'فشل  في عملية الشحن',
                'data' => [
                    'balance' => $wallet->balance,
                    'cardExpiry' => $wallet->card_expiry,
                    'cardNumber' => $wallet->card_number,
                ]
            ]);
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

    //دالة عرض صفحة الفواتير
    public function invoices(Auction $auction)
    {
        $auctionWinner = AuctionWinner::where('auction_id', '=', $auction->id)->first();


        return response()->json([
            'status' => true,
            'message' => Messages::getMessage('SUCCESS_SEND'),
            'allData' => [
                'id' => $auction->id ?? '',
                'serial_number' => $auction->serial_number ?? '',
                'name' => $auction->name ?? '',
                'quantity' => $auction->quantity ?? '',
                'sellPrice' => $auctionWinner->winning_amount . ' ' . 'ر.س' ?? '',
                'image' =>  $auction->account->image_profile ?? '',
                'sellerName' => $auction->account->name ?? '',
                'sellerAddress' => $auction->account->countries->name ?? '',

            ]
        ]);
    }


    //دالة اغلاق المزاد من الموبايل
    public function closeMobileAuctions($id)
    {
        $auction = Auction::where('id', '=', $id)->first();
        if ($auction->status == 'waiting') {
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
