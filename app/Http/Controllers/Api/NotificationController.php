<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    //

    public function index()
    {
        $notifications = Notification::with('account')->orderBy('created_at', 'desc')->get();

        return response()->view('cms.Notification.index', ['notifications' => $notifications]);
    }

    public function create()
    {
        return response()->view('cms.Notification.create');
    }


    public function sendNotification(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'details' => 'required|string',
            'account_id' => 'nullable|exists:accounts,id', // تحقق من وجود المستخدم
            'is_for_all' => 'required|boolean',
        ]);

        // إنشاء الإشعار
        $notification = Notification::create([
            'title' => $request->title,
            'details' => $request->details,
            'account_id' => $request->is_for_all ? null : $request->account_id, // إذا كان للجميع، اترك user_id فارغًا
            'is_read' => false,
            'is_for_all' => $request->is_for_all,
            'status' => $request->is_for_all ? 'all' : 'tajer',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'الإشعار تم إرساله بنجاح',
            'notification' => $notification,
        ]);
    }


    public function getAllUsers()
    {
        $users = Account::all(['id', 'name']); // جلب فقط معرف واسم المستخدم
        return response()->json(['users' => $users]);
    }
}
