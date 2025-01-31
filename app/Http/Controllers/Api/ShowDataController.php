<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Messages;
use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\Condition;
use App\Models\ContactUs;
use App\Models\Notification;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class ShowDataController extends Controller
{
    //
    public function showProducts()
    {
        $data = Product::where('active', '=', true)->get()->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'image' => url('storage/',  $product->image),
            ];
        });

        return response()->json([
            'status' => true,
            'message' => Messages::getMessage('SUCCESS_SEND'),
            'data' => $data,
        ]);
    }

    public function seendContactUs(Request $request)
    {
        $validator = Validator($request->all(), [
            'title' => 'required|string|min:3|max:255',
            'details' => 'required|string|min:3',
        ]);
        if (!$validator->fails()) {
            $contactUs = new ContactUs();
            $contactUs->title  = $request->get('title');
            $contactUs->details  = $request->get('details');
            $contactUs->account_id = Auth::id();
            $isSaved = $contactUs->save();
            return response()->json(
                [
                    'statue' => $isSaved ? true : false,
                    'message' => $isSaved ?  'تم الإرسال  بنجاح سيتم التواصل معك ان لزم الامر' : Messages::getMessage('REGISTRATION_FAILED'),
                ],
                $isSaved ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST
            );
        } else {
            return response()->json(['message' => $validator->getMessageBag()->first()], Response::HTTP_BAD_REQUEST);
        }
    }


    public function showConditions()
    {
        $data = Condition::where('active', '=', true)->get()->map(function ($product) {
            return [
                'id' => $product->id,
                'title' => $product->title,
                'sub_title' => $product->sub_title,
            ];
        });

        return response()->json([
            'status' => true,
            'message' => Messages::getMessage('SUCCESS_SEND'),
            'data' => $data,
        ]);
    }


    public function createProduct(Request $request)
    {
        $validator = Validator($request->all(), [
            'name' => 'required|string|unique:countries|min:3',
            'image' => 'required|image|max:2048|mimes:jpg,png',
        ]);

        if (!$validator->fails()) {
            $product = new Product();
            $product->name = $request->input('name');
            if ($request->hasFile('image')) {
                $imageName = time() . '_' . str_replace(' ', '', $product->name) . '.' . $request->file('image')->extension();
                $request->file('image')->storePubliclyAs('Product', $imageName, ['disk' => 'public']);
                $product->image = 'Product/' . $imageName;
            }
            $isSaved = $product->save();
            return response()->json(
                [
                    'statue' => $isSaved ? true : false,
                    'message' => $isSaved ?  Messages::getMessage('SUCCESS_SEND') : Messages::getMessage('REGISTRATION_FAILED'),
                    'data' => [
                        'id' => $product->id,
                        'name' => $product->name,
                        'image' => url('storage/',  $product->image),
                    ],
                ],
                $isSaved ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST
            );
        } else {
            return response()->json(["message" => $validator->getMessageBag()->first()], Response::HTTP_BAD_REQUEST);
        };
    }


    public function getNotifications(Request $request)
    {
        $user = Auth::user();
        $notifications = Notification::where(function ($query) use ($user) {
            $query->where('account_id', $user->id)
                ->orWhere('is_for_all', true);
        })->orderBy('created_at', 'desc')->get()->map(function ($notification) {
            return [
                'id' => $notification->id ?? '',
                'title' => $notification->title ?? '',
                'details' => $notification->details ?? '',
                'is_read' => $notification->is_read ?? '',
                'status' => $notification->status ?? '',
                'name_moz' => $notification->name_moza ?? '',
                'image_moz' => $notification->image_moza ?? '',
                'city_moz' => $notification->city_moza ?? '',
                'created_at' => $notification->created_at->format('h:i A') ?? '',
            ];
        });

        Notification::where('account_id', $user->id)
            ->orWhere('is_for_all', true)
            ->update(['is_read' => true]);

        return response()->json([
            'status' => true,
            'message' => 'الإشعارات تم جلبها بنجاح',
            'notifications' => $notifications,
        ], 200);
    }
}
