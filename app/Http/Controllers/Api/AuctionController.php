<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Messages;
use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\Offer;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Notifications\Action;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class AuctionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Auction::where('account_id', '=', Auth::id())->get()->map(function ($auction) {
            return [
                'id' => $auction->id ?? '', //الid
                'image' => url('storage',  $auction->image) ?? '', // صورةالمنتج
                'name' => $auction->name ?? '', //اسم المنتج
                'product' => $auction->product->name ?? '', //نوع المنتج
                'quantity' => $auction->quantity ?? '', // كمية المنتج
                'starting_price' => $auction->starting_price ?? '', //السعرالابتدائي للمنتج
                'status' => $auction->status ?? '', //حالة المنتج
                'address' => $auction->address ?? '',
                'end_time' =>  Carbon::parse($auction->end_time)->setTimezone('Asia/Riyadh')->format('g:i:s A'),
                'created_at' => $auction->created_at->format('g:i:s A'),
            ];
        });

        return response()->json([
            'status' => true,
            'message' => Messages::getMessage('SUCCESS_SEND'),
            'data' => $data,
        ]);
    }

    public function indexWebDetails(Auction $auction)
    {
        $offers = Offer::where('auction_id', '=', $auction->id)->get();
        $offer_amount = Offer::where('auction_id', '=', $auction->id)->where('status', '=', 'accepted')->first();
        return response()->view('cms.Auctions.indexDetailse', [
            'auction' => $auction,
            'offers' => $offers,
            'offer_amount' => $offer_amount,
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpg,png,svg', // تحقق من الصورة (اختياري)
            'product_type' => 'required|exists:products,id', // تحقق من وجود المنتج في قاعدة البيانات
            'name' => 'required|string|min:3|max:255', // تحقق من الاسم
            'quantity' => 'required|string|min:1', // تحقق من الكمية (يجب أن تكون عدد صحيح وأكبر من 0)
            'starting_price' => 'required|numeric|min:0', // تحقق من السعر الابتدائي (يجب أن يكون عدد صحيح أو عشري)
            'address' => 'required|string|min:3|max:255',
        ]);
        if (!$validator->fails()) {
            $now = Carbon::now('Asia/Riyadh');
            // إذا كان نوع المنتج 1، ممنوع إنشاء المزاد من الساعة 10 للساعة 11
            if ($request->get('product_type') == 1 && $now->between(Carbon::parse('09:00:00'), Carbon::parse('10:00:00'))) {
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'لا يمكنك إنشاء مزاد لهذا المنتج من الساعة 10 صباحاً إلى الساعة 11 صباحاً.'
                    ],
                    Response::HTTP_BAD_REQUEST
                );
            }
            // إذا كان نوع المنتج 2، ممنوع إنشاء المزاد من الساعة 11 للساعة 12
            if ($request->get('product_type') == 2 && $now->between(Carbon::parse('10:00:00'), Carbon::parse('11:00:00'))) {
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'لا يمكنك إنشاء مزاد لهذا المنتج من الساعة 11 صباحاً إلى الساعة 12 ظهراً.'
                    ],
                    Response::HTTP_BAD_REQUEST
                );
            }
            $auction = new Auction();
            $auction->serial_number = self::generateSerialNumber();
            if ($request->hasFile('image')) {
                $imageName = time() . '_' . str_replace(' ', '', $auction->name) . '.' . $request->file('image')->extension();
                $request->file('image')->storePubliclyAs('Auction', $imageName, ['disk' => 'public']);
                $auction->image = 'Auction/' . $imageName;
            }
            $auction->name  = $request->get('name');
            $auction->quantity  = $request->get('quantity');
            $auction->starting_price  = $request->get('starting_price');
            $auction->status  = 'waiting';
            $auction->product_id  = $request->get('product_type');
            $auction->account_id  = Auth::id();
            $auction->address = $request->get('address');
            $isSaved = $auction->save();
            return response()->json(
                [
                    'statue' => $isSaved ? true : false,
                    'message' => $isSaved ?  Messages::getMessage('CREATE_SUCCESS') : Messages::getMessage('CREATE_FAILED'),
                    'data' =>  [
                        'id' => $auction->id ?? '', //الid
                        'name' => $auction->name ?? '', //اسم المنتج
                        'image' => url('storage',  $auction->image), // صورةالمنتج
                        'quantity' => $auction->quantity ?? '', // كمية المنتج
                        'product_id' => (int) ($auction->product_id ?? 0),
                        'status' => $auction->status ?? '', //حالة المنتج
                    ],
                ],
                $isSaved ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST
            );
        } else {
            return response()->json(['message' => $validator->getMessageBag()->first()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Auction $auction)
    {
        //
    }


    /**
     * Display the specified resource.
     */
    public function indexWeb()
    {
        $data = Auction::with('account')->get();
        return response()->view('cms.Auctions.index', ['data' => $data]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,  $id)
    {
        $auction = Auction::where('id', '=', $id)->first();
        $validator = Validator::make($request->all(), [
            'image' => 'nullable|image|mimes:jpg,png,svg',
            'product_type' => 'required|exists:products,id',
            'name' => 'required|string|min:3|max:255',
            'quantity' => 'required|string|min:1',
            'starting_price' => 'required|numeric|min:0',
        ]);
        if (!$validator->fails()) {

            if ($request->hasFile('image')) {
                if ($auction->image !== null) {
                    Storage::disk('public')->delete($auction->image);
                }
                $imageName = time() . '_' . str_replace(' ', '', $auction->name) . '.' . $request->file('image')->extension();
                $request->file('image')->storePubliclyAs('Auction', $imageName, ['disk' => 'public']);
                $auction->image = 'Auction/' . $imageName;
            }
            $auction->name  = $request->get('name');
            $auction->quantity  = $request->get('quantity');
            $auction->starting_price  = $request->get('starting_price');
            $auction->status  = 'waiting';
            $auction->product_id  = $request->get('product_type');
            $auction->account_id  = Auth::id();
            $isSaved = $auction->save();
            return response()->json(
                [
                    'statue' => $isSaved ? true : false,
                    'message' => $isSaved ?  Messages::getMessage('UPDATE_SUCCESS') : Messages::getMessage('UPDATE_FAILED'),
                    'data' =>  [
                        'id' => $auction->id ?? '', //الid
                        'name' => $auction->name ?? '', //اسم المنتج
                        'product' => $auction->product->name ?? '', //نوع المنتج
                        'quantity' => $auction->quantity ?? '', // كمية المنتج
                        'starting_price' => $auction->starting_price ?? '', //السعرالابتدائي للمنتج
                        'status' => $auction->status ?? '', //حالة المنتج
                        'image' => url('storage',  $auction->image), // صورةالمنتج
                    ],
                ],
                $isSaved ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST
            );
        } else {
            return response()->json(['message' => $validator->getMessageBag()->first()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $auction = Auction::where('id', '=', $id)->first();
        $isDelete = $auction->delete();
        if ($isDelete) {
            if ($auction->image !== Null) {
                Storage::disk('public')->delete($auction->image);
            }
        }
        return response()->json([
            'message' => $isDelete ?  Messages::getMessage('DELETE_SUCCESS') : Messages::getMessage('DELETE_FAILED'),
        ], $isDelete ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST);
    }



    // دالة لتوليد الرقم التسلسلي الفريد من 7 أرقام
    private function generateSerialNumber()
    {
        do {
            $serial = mt_rand(1000000, 9999999); // توليد رقم عشوائي مكون من 7 أرقام
        } while (Auction::where('serial_number', $serial)->exists()); // استدعاء where من الموديل

        return $serial;
    }
}
