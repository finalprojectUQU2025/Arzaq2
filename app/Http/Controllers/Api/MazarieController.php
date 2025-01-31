<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Messages;
use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\AuctionWinner;
use App\Models\Offer;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class MazarieController extends Controller
{
    //دالة عرض صفحة الرئيسية
    public function homeScreen()
    {
        $myWallet = Wallet::where('account_id', '=', Auth::id())->first();
        $allProduct = Product::all();
        $allTransactions = Auction::where('account_id', Auth::id())
            ->whereMonth('created_at', Carbon::now()->month) // الشهر الحالي
            ->whereYear('created_at', Carbon::now()->year) // السنة الحالية
            ->get();
        $data = Auction::where('status', '=', 'waiting')->get()->map(function ($order) {
            return [
                'id' => $order->id ?? '',
                'name' => $order->name ?? '',
                'image' => url('storage/' . $order->image),
                'quantity' => $order->quantity ?? '',
                'product_id' => $order->product_id ?? '',
                'status' => $order->status ?? '', //حالة المنتج

            ];
        });


        return response()->json([
            'status' => true,
            'message' => Messages::getMessage('SUCCESS_SEND'),
            'allData' => [
                'balance' => $myWallet->balance . ' ' . 'ر.س' ?? '',
                'product' => $allProduct->count(), // مجموع المنتجات
                'transactions' => $allTransactions->count(), //المعامللات
                'orders' => $data->count(),
                'data' => $data,
            ],
        ]);
    }

    //دالة عرض صفحة مبيعاتي
    public function mySales()
    {
        $myWallet = Wallet::where('account_id', '=', Auth::id())->first();
        $data = Auction::where('status', '=', 'done')
            ->where('account_id', '=', Auth::id())
            ->with('auctionWinners')
            ->get()->map(function ($mySale) {
                return [
                    'id' => $mySale->id,
                    'name' => $mySale->name,
                    'image' => url('storage/' . $mySale->image),
                    'quantity' => $mySale->quantity,
                    'buyer' => $mySale->auctionWinners->first()->account->name,
                    'price' => $mySale->auctionWinners->first()->winning_amount . ' ' . 'ر.س',
                    'created_at' => Carbon::parse($mySale->created_at)->format('d/m/Y - h:i A'),
                ];
            });
        return response()->json([
            'status' => true,
            'message' => Messages::getMessage('SUCCESS_SEND'),
            'allData' => [
                'allSale' => $data->count(),
                'profits' => $myWallet->balance . ' ' . 'ر.س' ?? '',
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
                'totalOffer' => $offers->count(),
                'status' => $auction->status ?? '', //حالة المنتج
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

    //دالة عرض صفحة الفواتير
    public function invoices(Auction $auction)
    {

        $auctionWinner = AuctionWinner::where('auction_id', '=', $auction->id)->first();
        $response = [
            'status' => true,
            'message' => Messages::getMessage('SUCCESS_SEND'),
        ];
        if ($auctionWinner !== null) {
            $response['allData'] = [
                'id' => $auction->id ?? '',
                'serial_number' => $auction->serial_number ?? '',
                'name' => $auction->name ?? '',
                'quantity' => $auction->quantity ?? '',
                'sellPrice' => ($auctionWinner->winning_amount ?? '0') . ' ر.س',
                'image' => url($auction->account->image_profile) ?? '',
                'buyerName' => $auctionWinner->account->name ?? '',
                'buyerAddress' => $auctionWinner->account->countries->name ?? '',
            ];
        } else {
            $response['status'] = false;
            $response['message'] = 'عذرا لا يوجد فاتورة بسبب عدم  بيع المزاد.';
        }
        return response()->json($response, 200);
    }

    //دالة عرض صفحة محفظتي
    public function myWallet(Request $request)
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
                'balance' => $myWallet->balance ?? '',
                'cardExpiry' =>  $myWallet->card_expiry ?? '',
                'cardNumber' =>  $myWallet->card_number ?? '',
                'myTransaction' => $myTransaction,
            ]
        ]);
    }
}
