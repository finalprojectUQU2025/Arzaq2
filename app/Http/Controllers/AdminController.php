<?php

namespace App\Http\Controllers;

use App\Mail\AdminWelcomeEmail;
use App\Models\Admin;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $data = Admin::with('country')->get();
        return response()->view('cms.Admins.index', ['data' => $data]);
    }

    public function adminBlocked($id)
    {
        $admin = Admin::findOrFail($id);
        if ($admin->id === 1) {
            return response()->json(
                ['message' => 'عذرا لا يمكنك حظر المشرف الرئيسي'],
                Response::HTTP_BAD_REQUEST
            );
        } elseif (auth('admin')->id() === $admin->id) {
            return response()->json(
                ['message' => 'لا يمكنك حظر نفسك'],
                Response::HTTP_BAD_REQUEST
            );
        } else {
            $admin->blocked = !$admin->blocked;
            $isSaved = $admin->save();
            return response()->json(
                [
                    'status' => true,
                    'message' => 'تم الحظر بنجاح'
                ],
                Response::HTTP_OK
            );
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $countries = Country::where('active', true)->get();
        return response()->view('cms.Admins.create', ['countries' => $countries]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator($request->all(), [
            'name' => 'required|string|min:3',
            'email' => 'required|string|email|unique:admins,email',
            'phone' => 'required|string|numeric|digits:10|unique:admins,phone',
            'id_number' => 'required|string|numeric|digits:9|unique:admins,id_number',
            'country_id' => 'required|integer|exists:countries,id',
            'image' => 'nullable|image|mimes:jpg,png,svg',
        ]);
        if (!$validator->fails()) {
            $admin = new Admin();
            $admin->name = $request->get('name');
            $admin->email = $request->get('email');
            $admin->phone = $request->get('phone');
            $admin->id_number = $request->get('id_number');
            $admin->country_id = $request->get('country_id');
            $randomPassword = Str::random(6);
            $admin->password = Hash::make($randomPassword);
            if ($request->hasFile('image')) {
                $imageName = time() . '_' . str_replace(' ', '', $admin->name) . '.' . $request->file('image')->extension();
                $request->file('image')->storePubliclyAs('Admin', $imageName, ['disk' => 'public']);
                $admin->image = 'Admin/' . $imageName;
            }
            $isSaved = $admin->save();
            if ($isSaved) {
                Mail::to($admin)->send(new AdminWelcomeEmail($admin->email, $admin->name, $randomPassword));
            }
            return response()->json(['message' => $isSaved ?  'تم إنشاء المسؤول بنجاح' : 'حدث خطأ أثناء الإنشاء'], $isSaved ? Response::HTTP_CREATED : Response::HTTP_BAD_REQUEST);
        } else {
            return response()->json(['message' => $validator->getMessageBag()->first()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Admin $admin)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Admin $admin)
    {
        $countries = Country::where('active', true)->get();
        return response()->view('cms.Admins.edit', [
            'countries' => $countries,
            'admin' => $admin,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Admin $admin)
    {
        $validator = Validator($request->all(), [
            'name' => 'required|string|min:3',
            'email' => 'required|string|email|unique:admins,email,' . $admin->id,
            'phone' => 'required|string|numeric|digits:10|unique:admins,phone,' . $admin->id,
            'id_number' => 'required|string|numeric|digits:9|unique:admins,id_number,' . $admin->id,
            'country_id' => 'required|integer|exists:countries,id',
            'image' => 'nullable|image|mimes:jpg,png,svg',
        ]);
        if (!$validator->fails()) {
            $admin->name = $request->get('name');
            $admin->email = $request->get('email');
            $admin->phone = $request->get('phone');
            $admin->id_number = $request->get('id_number');
            $admin->country_id = $request->get('country_id');
            if ($request->hasFile('image')) {
                if ($admin->image !== Null) {
                    Storage::disk('public')->delete($admin->image);
                }
                $imageName = time() . '_' . str_replace(' ', '', $admin->name) . '.' . $request->file('image')->extension();
                $request->file('image')->storePubliclyAs('Admin', $imageName, ['disk' => 'public']);
                $admin->image = 'Admin/' . $imageName;
            }
            $isSaved = $admin->save();
            return response()->json(['message' => $isSaved ?  'تم تحديث البيانات بنجاح' : 'فشل في تحديث البيانات'], $isSaved ? Response::HTTP_CREATED : Response::HTTP_BAD_REQUEST);
        } else {
            return response()->json(['message' => $validator->getMessageBag()->first()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Admin $admin)
    {
        if (auth('admin')->id() === $admin->id) {
            return response()->json(
                ['message' => 'لا يمكنك حذف حسابك الخاص'],
                Response::HTTP_BAD_REQUEST
            );
        } elseif ($admin->id === 1) {
            return response()->json(
                ['message' => 'لا يمكن حذف حساب المدير الرئيسي'],
                Response::HTTP_BAD_REQUEST
            );
        } else {
            $isDelete = $admin->delete();
            if ($isDelete) {
                if ($admin->image !== null) {
                    Storage::disk('public')->delete($admin->image);
                }
            }
            return response()->json([
                'message' => $isDelete ? 'تم الحذف بنجاح' : 'حدث خطأ أثناء الحذف'
            ], $isDelete ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST);
        }
    }
}
